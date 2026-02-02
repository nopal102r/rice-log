(function (window) {
    // Minimal face recognition helper that dynamically loads tfjs and face-api if needed
    const FaceRecognitionHelper = {
        modelsLoaded: false,
        videoStream: null,
        detectionInterval: null,
        detectionActive: false,

        loadScript: function (src) {
            return new Promise((resolve, reject) => {
                // Check if already loaded
                const existing = Array.from(
                    document.getElementsByTagName("script"),
                ).find((s) => s.src && s.src.indexOf(src) !== -1);
                if (existing) {
                    existing.addEventListener("load", () => resolve());
                    if (
                        existing.readyState === "complete" ||
                        existing.readyState === "loaded"
                    )
                        resolve();
                    return;
                }
                const s = document.createElement("script");
                s.src = src;
                s.async = false;
                s.defer = false;
                s.onload = () => resolve();
                s.onerror = (e) => reject(new Error("Failed to load " + src));
                document.head.appendChild(s);
            });
        },

        initModels: async function (modelPath) {
            try {
                if (window.faceapi && this.modelsLoaded) return true;

                // Ensure tfjs present
                if (typeof tf === "undefined") {
                    await this.loadScript(
                        "https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4",
                    );
                    console.log("FaceRecognitionHelper: tfjs loaded");
                }
                // Ensure face-api present
                if (typeof faceapi === "undefined") {
                    await this.loadScript(
                        "https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js",
                    );
                    console.log("FaceRecognitionHelper: face-api loaded");
                }

                // Use provided modelPath or default
                const base = modelPath || window.location.origin + "/models/";

                // load smallest models needed
                await faceapi.nets.tinyFaceDetector.loadFromUri(base);
                await faceapi.nets.faceLandmark68TinyNet
                    .loadFromUri(base)
                    .catch(() =>
                        faceapi.nets.faceLandmark68Net.loadFromUri(base),
                    );
                await faceapi.nets.faceRecognitionNet.loadFromUri(base);
                await faceapi.nets.faceExpressionNet
                    .loadFromUri(base)
                    .catch(() => {});

                this.modelsLoaded = true;
                return true;
            } catch (err) {
                console.error("initModels error", err);
                return false;
            }
        },

        startCamera: async function (videoElement, width = 640, height = 480) {
            if (!videoElement) throw new Error("No video element provided");
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width, height },
                    audio: false,
                });
                this.videoStream = stream;
                videoElement.srcObject = stream;
                await videoElement.play().catch(() => {});
                return true;
            } catch (err) {
                console.error("startCamera error", err);
                throw err;
            }
        },

        stopCamera: function () {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach((t) => t.stop());
                this.videoStream = null;
            }
        },

        startDetection: function (
            videoElement,
            canvasElement,
            onDetectionChange,
        ) {
            if (typeof faceapi === "undefined") {
                console.warn(
                    "startDetection: faceapi not loaded, attempting to init models",
                );
                // Try to initialize models (will also load face-api if missing)
                // Note: initModels requires a model path; use default models folder
                return this.initModels().then((success) => {
                    if (!success) throw new Error("faceapi failed to load");
                    return this.startDetection(
                        videoElement,
                        canvasElement,
                        onDetectionChange,
                    );
                });
            }
            if (this.detectionActive) return;
            this.detectionActive = true;

            const displaySize = {
                width: videoElement.videoWidth || videoElement.clientWidth,
                height: videoElement.videoHeight || videoElement.clientHeight,
            };
            if (canvasElement) {
                canvasElement.width = displaySize.width;
                canvasElement.height = displaySize.height;
            }

            const options = new faceapi.TinyFaceDetectorOptions({
                inputSize: 224,
                scoreThreshold: 0.5,
            });

            const loop = async () => {
                if (!this.detectionActive) return;
                try {
                    const detections = await faceapi
                        .detectAllFaces(videoElement, options)
                        .withFaceLandmarks(true)
                        .withFaceExpressions()
                        .withFaceDescriptors();
                    if (canvasElement && detections && detections.length > 0) {
                        const ctx = canvasElement.getContext("2d");
                        ctx.clearRect(
                            0,
                            0,
                            canvasElement.width,
                            canvasElement.height,
                        );
                        try {
                            const displaySize = {
                                width: canvasElement.width,
                                height: canvasElement.height,
                            };
                            faceapi.matchDimensions(canvasElement, displaySize);
                            const resizedDetections = faceapi.resizeResults(
                                detections,
                                displaySize,
                            );
                            faceapi.draw.drawDetections(
                                canvasElement,
                                resizedDetections,
                            );
                            faceapi.draw.drawFaceLandmarks(
                                canvasElement,
                                resizedDetections,
                            );
                        } catch (drawErr) {
                            console.warn("draw error", drawErr);
                        }
                    }
                    const found = detections && detections.length > 0;
                    if (typeof onDetectionChange === "function")
                        onDetectionChange(found, detections);
                } catch (err) {
                    console.error("detection loop error", err);
                }
                this.detectionInterval = setTimeout(loop, 200);
            };

            loop();
        },

        stopDetection: function () {
            this.detectionActive = false;
            if (this.detectionInterval) clearTimeout(this.detectionInterval);
            this.detectionInterval = null;
        },

        getFaceDescriptors: async function (input) {
            // input can be video element, image element, or canvas
            if (typeof faceapi === "undefined") {
                console.warn(
                    "getFaceDescriptors: faceapi not loaded, attempting to init models",
                );
                const ok = await this.initModels();
                if (!ok) throw new Error("faceapi not loaded");
            }
            try {
                const options = new faceapi.TinyFaceDetectorOptions();
                const results = await faceapi
                    .detectAllFaces(input, options)
                    .withFaceLandmarks(true)
                    .withFaceDescriptors();
                if (!results) return [];
                return results.map((r) => r.descriptor);
            } catch (err) {
                console.error("getFaceDescriptors error", err);
                return [];
            }
        },

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
    };

    window.FaceRecognitionHelper = FaceRecognitionHelper;
})(window);
