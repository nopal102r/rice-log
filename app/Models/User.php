<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job',
        'date_of_birth',
        'phone',
        'address',
        'latitude',
        'longitude',
        'status',
        'last_presence_at',
        'face_data',
        'face_enrolled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_presence_at' => 'datetime',
            'face_data' => 'array',
            'face_enrolled' => 'boolean',
        ];
    }

    /**
     * Get the absences for the user.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Get the leave submissions for the user.
     */
    public function leaveSubmissions(): HasMany
    {
        return $this->hasMany(LeaveSubmission::class);
    }

    /**
     * Get the deposits for the user.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get the monthly summaries for the user.
     */
    public function monthlySummaries(): HasMany
    {
        return $this->hasMany(MonthlySummary::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if user is boss/manager
     */
    public function isBoss(): bool
    {
        return $this->role === 'bos';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'karyawan';
    }

    // Role Checks
    public function isDriver(): bool 
    {
        return $this->job === 'supir';
    }

    public function isFarmer(): bool {
        return $this->job === 'petani';
    }

    public function isMiller(): bool {
        return $this->job === 'ngegiling';
    }

    public function isSales(): bool {
        return $this->job === 'sales';
    }

    /**
     * Calculate age from date of birth
     */
    public function getAge(): int
    {
        if (!$this->date_of_birth) {
            return 0;
        }
        return $this->date_of_birth->diff(now())->y;
    }

    /**
     * Enroll face data
     */
    public function enrollFace(array $faceDescriptors): bool
    {
        try {
            $this->update([
                'face_data' => $faceDescriptors,
                'face_enrolled' => true,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error enrolling face for user ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if face is enrolled
     */
    public function hasFaceEnrolled(): bool
    {
        return $this->face_enrolled && !empty($this->face_data);
    }

    /**
     * Get face data for verification
     */
    public function getFaceData(): ?array
    {
        return $this->face_data;
    }
}