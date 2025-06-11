<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\QueueTicket;
use App\Models\Certificate;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            MedicalRecordSeeder::class,
            QueueTicketSeeder::class,
            CertificateSeeder::class,
        ]);
    }
}

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@clinic.edu.ph',
            'password' => Hash::make('admin123'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'role' => 'super_admin',
            'phone' => '+63-917-123-4567',
            'employee_id' => 'ADMIN001',
            'is_active' => true,
        ]);

        // Create Staff Users
        $staffUsers = [
            [
                'username' => 'dr.smith',
                'email' => 'dr.smith@clinic.edu.ph',
                'password' => Hash::make('doctor123'),
                'first_name' => 'John',
                'last_name' => 'Smith',
                'role' => 'physician',
                'phone' => '+63-917-234-5678',
                'employee_id' => 'DOC001',
            ],
            [
                'username' => 'nurse.jane',
                'email' => 'jane.doe@clinic.edu.ph',
                'password' => Hash::make('nurse123'),
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'role' => 'nurse',
                'phone' => '+63-917-345-6789',
                'employee_id' => 'NUR001',
            ],
            [
                'username' => 'dr.garcia',
                'email' => 'dr.garcia@clinic.edu.ph',
                'password' => Hash::make('dentist123'),
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'role' => 'dentist',
                'phone' => '+63-917-456-7890',
                'employee_id' => 'DEN001',
            ],
            [
                'username' => 'reception.mary',
                'email' => 'mary.reception@clinic.edu.ph',
                'password' => Hash::make('reception123'),
                'first_name' => 'Mary',
                'last_name' => 'Johnson',
                'role' => 'staff',
                'phone' => '+63-917-567-8901',
                'employee_id' => 'STA001',
            ],
        ];

        foreach ($staffUsers as $userData) {
            User::create($userData);
        }
    }
}

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'user_id' => 2, // Dr. Smith
                'first_name' => 'John',
                'last_name' => 'Smith',
                'specialization' => 'General Medicine',
                'email' => 'dr.smith@clinic.edu.ph',
                'phone' => '+63-917-234-5678',
                'license_number' => 'MD-2020-001234',
                'is_active' => true,
            ],
            [
                'user_id' => 4, // Dr. Garcia
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'specialization' => 'Dentistry',
                'email' => 'dr.garcia@clinic.edu.ph',
                'phone' => '+63-917-456-7890',
                'license_number' => 'DMD-2019-005678',
                'is_active' => true,
            ],
            [
                'user_id' => null,
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'specialization' => 'Pediatrics',
                'email' => 'dr.johnson@clinic.edu.ph',
                'phone' => '+63-917-678-9012',
                'license_number' => 'MD-2021-009876',
                'is_active' => true,
            ],
        ];

        foreach ($doctors as $doctorData) {
            Doctor::create($doctorData);
        }
    }
}

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'patient_number' => '2024-001001',
                'email' => 'juan.dela.cruz@student.bulacan.edu.ph',
                'password' => Hash::make('patient123'),
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'age' => 20,
                'sex' => 'Male',
                'date_of_birth' => '2004-01-15',
                'phone' => '+63-917-111-2222',
                'lot_number' => '123',
                'barangay_subdivision' => 'Barangay San Jose',
                'street' => 'Rizal Street',
                'city_municipality' => 'Malolos',
                'province' => 'Bulacan',
                'campus' => 'Malolos Campus',
                'college_office' => 'CICT',
                'course_designation' => 'BS Computer Science - 3rd Year',
                'patient_type' => 'student',
                'emergency_contact_name' => 'Maria Dela Cruz',
                'emergency_contact_relation' => 'Mother',
                'emergency_contact_number' => '+63-917-333-4444',
                'blood_type' => 'A+',
                'allergies' => 'None',
                'is_active' => true,
            ],
            [
                'patient_number' => 'FAC-2024-001',
                'email' => 'prof.santos@bulacan.edu.ph',
                'password' => Hash::make('faculty123'),
                'first_name' => 'Anna',
                'middle_name' => 'Maria',
                'last_name' => 'Santos',
                'age' => 35,
                'sex' => 'Female',
                'date_of_birth' => '1989-05-20',
                'phone' => '+63-917-555-6666',
                'barangay_subdivision' => 'Subdivision A',
                'street' => 'Acacia Street',
                'city_municipality' => 'San Jose del Monte',
                'province' => 'Bulacan',
                'campus' => 'Malolos Campus',
                'college_office' => 'CON',
                'course_designation' => 'Professor - Nursing Department',
                'patient_type' => 'faculty',
                'emergency_contact_name' => 'Roberto Santos',
                'emergency_contact_relation' => 'Husband',
                'emergency_contact_number' => '+63-917-777-8888',
                'blood_type' => 'O+',
                'allergies' => 'Penicillin',
                'is_active' => true,
            ],
            [
                'patient_number' => 'STA-2024-001',
                'email' => 'staff.reyes@bulacan.edu.ph',
                'password' => Hash::make('staff123'),
                'first_name' => 'Carlos',
                'middle_name' => 'Jose',
                'last_name' => 'Reyes',
                'age' => 28,
                'sex' => 'Male',
                'date_of_birth' => '1996-08-10',
                'phone' => '+63-917-999-0000',
                'street' => 'Mabini Street',
                'city_municipality' => 'Malolos',
                'province' => 'Bulacan',
                'campus' => 'Malolos Campus',
                'college_office' => 'HR',
                'course_designation' => 'HR Assistant',
                'patient_type' => 'non_academic',
                'emergency_contact_name' => 'Linda Reyes',
                'emergency_contact_relation' => 'Wife',
                'emergency_contact_number' => '+63-917-111-0000',
                'blood_type' => 'B+',
                'allergies' => 'None',
                'is_active' => true,
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }
    }
}
