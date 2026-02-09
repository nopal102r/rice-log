/**
 * Face Recognition Helper
 * Handles face detection, enrollment, and verification using face-api.js
 */
(function (window) {
    const FaceRecognitionHelper = {
        modelsLoaded: false,
        detectionRunning: false,
        detectionInterval: null,
        detectionMode: "tinyFaceDetector", // Force tiny for better real-time detection

        /**
         * Load script dynamically
         */
        loadScript: function (src) {
            return new Promise((resolve, reject) => {
                if (document.querySelector(`script[src="${src}"]`)) {
                    resolve();
                    return;
                }

                const script = document.createElement("script");
                script.src = src;
                script.async = true;
                script.defer = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        },

        /**
         * Initialize face recognition models
         * @param {string} modelPath - Path to models directory
         * @returns {Promise<boolean>}
         */
        initModels: async function (modelPath) {
            try {
                if (window.faceapi && this.modelsLoaded) return true;


                // Ensure face-api present
                if (typeof faceapi === "undefined") {
                    await this.loadScript(
                        "https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js",
                    );
                    console.log("FaceRecognitionHelper: face-api loaded");
                }

                // Use provided modelPath or default
                const base = modelPath || window.location.origin + "/models/";

                // load models needed for face recognition
                console.log("Loading face detection models from:", base);

                // Load Tiny Face Detector FIRST (better for real-time detection)
                try {
                    await faceapi.nets.tinyFaceDetector.loadFromUri(base);
                    console.log("✓ Tiny Face Detector loaded");
                    this.detectionMode = "tinyFaceDetector";
                } catch (e) {
                    console.log(
                        "Tiny Face Detector not available, trying SSD MobileNet v1...",
                    );
                    try {
                        await faceapi.nets.ssdMobilenetv1.loadFromUri(base);
                        console.log("✓ SSD MobileNet v1 detector loaded");
                        this.detectionMode = "ssdMobilenetv1";
                    } catch (e2) {
                        console.error("❌ Both detectors failed to load!");
                        throw new Error("No face detector models available");
                    }
                }

                // Load face landmarks (try Tiny first, fallback to standard)
                try {
                    await faceapi.nets.faceLandmark68TinyNet.loadFromUri(base);
                    console.log("✓ Face Landmark 68 Tiny loaded");
                } catch (e) {
                    console.log(
                        "Face Landmark 68 Tiny not available, loading standard...",
                    );
                    await faceapi.nets.faceLandmark68Net.loadFromUri(base);
                    console.log("✓ Face Landmark 68 standard loaded");
                }

                // Load face recognition model
                await faceapi.nets.faceRecognitionNet.loadFromUri(base);
                console.log("✓ Face Recognition Net loaded");

                this.modelsLoaded = true;
                console.log("✓ Face recognition models loaded successfully");
                return true;
            } catch (err) {
                console.error("❌ Error loading models:", err);
                return false;
            }
        },

        /**
         * Start camera stream
         * @param {HTMLVideoElement} videoElement - Video element
         * @returns {Promise<void>}
         */
        startCamera: async function (videoElement) {
            try {
                if (!videoElement) throw new Error("No video element provided");

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "user" },
                    audio: false,
                });

                videoElement.srcObject = stream;

                return new Promise((resolve) => {
                    videoElement.onloadedmetadata = () => {
                        videoElement.play();
                        resolve();
                    };
                });
            } catch (err) {
                console.error("Camera error:", err);
                throw new Error(
                    "Tidak bisa membuka kamera. Pastikan browser memiliki akses ke kamera.",
                );
            }
        },

        /**
         * Stop camera stream
         * @param {HTMLVideoElement} videoElement - Video element (optional)
         */
        stopCamera: function (videoElement) {
            if (videoElement && videoElement.srcObject) {
                const stream = videoElement.srcObject;
                const tracks = stream.getTracks();
                tracks.forEach((track) => track.stop());
                videoElement.srcObject = null;
            }
        },

        /**
         * Start real-time face detection
         * @param {HTMLVideoElement} videoElement - Video element
         * @param {HTMLCanvasElement} canvasElement - Canvas element for drawing
         * @param {Function} callback - Callback when face is detected/lost
         * @param {string} labelText - Optional text to display on validation
         */
        /**
         * Start real-time face detection
         * @param {HTMLVideoElement} videoElement - Video element
         * @param {HTMLCanvasElement} canvasElement - Canvas element for drawing
         * @param {Function} callback - Callback when face is detected/lost
         * @param {string} labelText - [Deprecated]
         * @param {Float32Array} enrolledDescriptor - Optional enrolled descriptor for real-time validation
         */
        startDetection: function (videoElement, canvasElement, callback, labelText = "", enrolledDescriptor = null) {
            if (this.detectionRunning) return;

            if (!videoElement || !canvasElement) {
                console.error("Video or canvas element not found");
                return;
            }

            this.detectionRunning = true;
            let lastVerificationTime = 0;
            let isMatch = false;

            const displaySize = {
                width: videoElement.width || videoElement.offsetWidth,
                height: videoElement.height || videoElement.offsetHeight,
            };

            faceapi.matchDimensions(canvasElement, displaySize);

            const detectFaces = async () => {
                if (!this.detectionRunning) return;

                try {
                    // Get detection options based on detector type
                    let detectionOptions;
                    if (this.detectionMode === "tinyFaceDetector") {
                        detectionOptions = new faceapi.TinyFaceDetectorOptions({
                            inputSize: 416,
                            scoreThreshold: 0.5,
                        });
                    } else {
                        detectionOptions = new faceapi.SsdMobilenetv1Options({
                            minConfidence: 0.5,
                        });
                    }

                    const detections = await faceapi
                        .detectAllFaces(videoElement, detectionOptions)
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    const resizedDetections = faceapi.resizeResults(
                        detections,
                        displaySize,
                    );

                    // Clear canvas
                    const ctx = canvasElement.getContext("2d");
                    ctx.clearRect(
                        0,
                        0,
                        canvasElement.width,
                        canvasElement.height,
                    );

                    // Real-time verification (Throttled to every 500ms)
                    const now = Date.now();
                    if (enrolledDescriptor && resizedDetections.length > 0 && now - lastVerificationTime > 500) {
                        // Check first face
                        isMatch = this.verifyFace(resizedDetections[0].descriptor, enrolledDescriptor, 0.45);
                        lastVerificationTime = now;
                    }

                    // Draw detections
                    resizedDetections.forEach((detection) => {
                        const box = detection.detection.box;
                        
                        // Set color based on match status if enrolled data exists
                        if (enrolledDescriptor) {
                            ctx.strokeStyle = isMatch ? "#00FF00" : "#FF0000"; // Green vs Red
                            ctx.lineWidth = 3;
                        } else {
                            ctx.strokeStyle = "#00FF00"; // Default Green
                            ctx.lineWidth = 2;
                        }

                        ctx.strokeRect(box.x, box.y, box.width, box.height);

                        // Draw landmarks
                        if (detection.landmarks) {
                            ctx.fillStyle = enrolledDescriptor ? (isMatch ? "#00FF00" : "#FF0000") : "#FF0000";
                            const points = detection.landmarks.positions;
                            points.forEach((point) => {
                                ctx.beginPath();
                                ctx.arc(point.x, point.y, 2, 0, 2 * Math.PI);
                                ctx.fill();
                            });
                        }
                    });

                    // Callback with detection status
                    if (callback) {
                        callback(resizedDetections.length > 0, isMatch);
                    }
                } catch (err) {
                    console.error("Detection error:", err);
                }

                this.detectionInterval = requestAnimationFrame(detectFaces);
            };

            detectFaces();
        },

        /**
         * Stop real-time face detection
         */
        stopDetection: function () {
            this.detectionRunning = false;
            if (this.detectionInterval) {
                cancelAnimationFrame(this.detectionInterval);
                this.detectionInterval = null;
            }
        },

        /**
         * Get face descriptors from video/image element
         * @param {HTMLVideoElement|HTMLImageElement} input - Video or image element
         * @returns {Promise<Float32Array[]>} - Array of face descriptors
         */
        getFaceDescriptors: async function (input) {
            if (!input) throw new Error("No input element");

            try {
                if (!window.faceapi || !this.modelsLoaded) {
                    if (!window.faceapi) return [];
                }

                // Use SSD MobileNet v1 if available, otherwise Tiny Face Detector
                let options;
                if (faceapi.nets.ssdMobilenetv1?.isLoaded) {
                    options = new faceapi.SsdMobilenetv1Options();
                } else {
                    options = new faceapi.TinyFaceDetectorOptions();
                }

                const results = await faceapi
                    .detectAllFaces(input, options)
                    .withFaceLandmarks(faceapi.nets.faceLandmark68TinyNet.isLoaded)
                    .withFaceDescriptors();

                if (!results) return [];

                return results.map((r) => r.descriptor);
            } catch (err) {
                console.error("getFaceDescriptors error", err);
                return [];
            }
        },

        /**
         * Capture face image from video element
         * @param {HTMLVideoElement} videoElement - Video element
         * @returns {Promise<Blob>}
         */
        captureFace: async function (videoElement) {
            if (!videoElement) throw new Error("No video element");

            const canvas = document.createElement("canvas");
            canvas.width = videoElement.videoWidth || videoElement.clientWidth;
            canvas.height =
                videoElement.videoHeight || videoElement.clientHeight;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

            return new Promise((resolve) =>
                canvas.toBlob(resolve, "image/jpeg"),
            );
        },

        /**
         * Compare two face descriptors
         * Calculates euclidean distance between descriptors
         * @param {Float32Array} descriptor1 - First face descriptor
         * @param {Float32Array} descriptor2 - Second face descriptor
         * @returns {number} - Distance between descriptors (lower = more similar)
         */
        compareFaceDescriptors: function (descriptor1, descriptor2) {
            if (!descriptor1 || !descriptor2) return Infinity;

            let sum = 0;
            for (let i = 0; i < descriptor1.length; i++) {
                const diff = descriptor1[i] - descriptor2[i];
                sum += diff * diff;
            }
            return Math.sqrt(sum);
        },

        /**
         * Verify if a captured face matches enrolled face
         * @param {Float32Array} capturedDescriptor - Face descriptor from camera
         * @param {Float32Array} enrolledDescriptor - Stored face descriptor
         * @param {number} threshold - Matching threshold (default 0.6)
         * @returns {boolean} - True if match, false otherwise
         */
        verifyFace: function (
            capturedDescriptor,
            enrolledDescriptor,
            threshold = 0.45,
        ) {
            if (!capturedDescriptor || !enrolledDescriptor) return false;
            
            // Ensure both are arrays/typed arrays and have correct length (FaceAPI uses 128)
            if (capturedDescriptor.length !== 128 || enrolledDescriptor.length !== 128) {
                console.error("Descriptor length mismatch or invalid:", capturedDescriptor.length, enrolledDescriptor.length);
                return false;
            }

            const distance = this.compareFaceDescriptors(
                capturedDescriptor,
                enrolledDescriptor,
            );
            console.log("Face distance:", distance, "Threshold:", threshold);

            return distance < threshold;
        },

        /**
         * Verify multiple face samples against enrolled descriptor
         * Uses average distance for better accuracy
         * @param {Float32Array[]} capturedDescriptors - Array of captured descriptors
         * @param {Float32Array} enrolledDescriptor - Stored face descriptor
         * @param {number} threshold - Matching threshold
         * @returns {boolean}
         */
        verifyFaceMultiple: function (
            capturedDescriptors,
            enrolledDescriptor,
            threshold = 0.6,
        ) {
            if (
                !capturedDescriptors ||
                capturedDescriptors.length === 0 ||
                !enrolledDescriptor
            ) {
                return false;
            }

            // Calculate average distance from multiple samples
            let totalDistance = 0;
            capturedDescriptors.forEach((descriptor) => {
                totalDistance += this.compareFaceDescriptors(
                    descriptor,
                    enrolledDescriptor,
                );
            });
            const avgDistance = totalDistance / capturedDescriptors.length;

            console.log(
                "Average face distance:",
                avgDistance,
                "Threshold:",
                threshold,
            );
            return avgDistance < threshold;
        },
    };

    window.FaceRecognitionHelper = FaceRecognitionHelper;
})(window);
