<?php
/**
 * API Integration Layer
 * This file demonstrates how web views would integrate with Laravel APIs
 * For demo purposes, it simulates API responses
 */

class ClinicAPI {
    private $base_url = 'http://localhost:8000/api/';
    private $token = null;

    public function __construct($token = null) {
        $this->token = $token;
    }

    /**
     * Authentication Methods
     */
    public function login($email, $password, $role = 'patient') {
        // Simulate API call
        $demo_users = [
            'patient' => [
                'email' => 'john.doe@university.edu',
                'password' => 'password123',
                'data' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'patient_id' => 'STU-2023-001',
                    'role' => 'patient',
                    'token' => 'demo_patient_token_' . time()
                ]
            ],
            'staff' => [
                'email' => 'dr.smith@clinic.edu',
                'password' => 'staff123',
                'data' => [
                    'id' => 2,
                    'name' => 'Dr. Sarah Smith',
                    'staff_id' => 'DOC-2023-001',
                    'role' => 'doctor',
                    'token' => 'demo_staff_token_' . time()
                ]
            ],
            'admin' => [
                'email' => 'admin@clinic.edu',
                'password' => 'admin123',
                'data' => [
                    'id' => 3,
                    'name' => 'System Administrator',
                    'admin_id' => 'ADM-2023-001',
                    'role' => 'admin',
                    'token' => 'demo_admin_token_' . time()
                ]
            ]
        ];

        if (isset($demo_users[$role]) && 
            $demo_users[$role]['email'] === $email && 
            $demo_users[$role]['password'] === $password) {
            
            $this->token = $demo_users[$role]['data']['token'];
            return [
                'success' => true,
                'user' => $demo_users[$role]['data'],
                'token' => $this->token
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    /**
     * Patient Methods
     */
    public function getPatientDashboard($patient_id) {
        // Simulate API response
        return [
            'success' => true,
            'data' => [
                'patient' => [
                    'id' => $patient_id,
                    'name' => 'John Doe',
                    'email' => 'john.doe@university.edu',
                    'phone' => '+1 (555) 123-4567',
                    'student_id' => 'STU-2023-001',
                    'status' => 'Active'
                ],
                'upcoming_appointments' => [
                    [
                        'id' => 1,
                        'date' => '2024-06-17',
                        'time' => '2:00 PM',
                        'doctor' => 'Dr. Smith',
                        'purpose' => 'Follow-up consultation',
                        'status' => 'Confirmed',
                        'location' => 'Room 201'
                    ],
                    [
                        'id' => 2,
                        'date' => '2024-06-24',
                        'time' => '10:00 AM',
                        'doctor' => 'Dr. Johnson',
                        'purpose' => 'Annual check-up',
                        'status' => 'Scheduled',
                        'location' => 'Room 105'
                    ]
                ],
                'medical_history' => [
                    [
                        'id' => 1,
                        'date' => '2024-06-10',
                        'diagnosis' => 'Viral infection',
                        'doctor' => 'Dr. Smith',
                        'treatment' => 'Paracetamol 500mg, Rest',
                        'notes' => 'Patient responded well to treatment'
                    ],
                    [
                        'id' => 2,
                        'date' => '2024-05-15',
                        'diagnosis' => 'Health screening',
                        'doctor' => 'Dr. Lee',
                        'treatment' => 'All parameters normal',
                        'notes' => 'Annual health checkup completed'
                    ]
                ],
                'queue_position' => null,
                'notifications' => [
                    [
                        'type' => 'appointment_reminder',
                        'message' => 'Reminder: You have an appointment tomorrow at 2:00 PM',
                        'created_at' => '2024-06-16 09:00:00'
                    ]
                ]
            ]
        ];
    }

    public function bookAppointment($patient_id, $appointment_data) {
        // Simulate appointment booking
        return [
            'success' => true,
            'message' => 'Appointment booked successfully',
            'appointment' => [
                'id' => rand(100, 999),
                'date' => $appointment_data['date'],
                'time' => $appointment_data['time'],
                'doctor' => $appointment_data['doctor'],
                'purpose' => $appointment_data['purpose'],
                'status' => 'Scheduled',
                'confirmation_code' => 'APT-' . strtoupper(substr(md5(time()), 0, 8))
            ]
        ];
    }

    public function joinQueue($patient_id, $service_type = 'consultation') {
        // Simulate queue joining
        $queue_number = 'Q' . str_pad(rand(50, 150), 3, '0', STR_PAD_LEFT);
        
        return [
            'success' => true,
            'message' => 'Successfully joined queue',
            'queue_ticket' => [
                'number' => $queue_number,
                'service' => ucfirst($service_type),
                'estimated_wait' => rand(10, 30) . ' minutes',
                'position' => rand(3, 8),
                'issued_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Staff Methods
     */
    public function getStaffDashboard($staff_id) {
        return [
            'success' => true,
            'data' => [
                'staff' => [
                    'id' => $staff_id,
                    'name' => 'Dr. Sarah Smith',
                    'role' => 'Doctor',
                    'department' => 'General Medicine',
                    'schedule_today' => '9:00 AM - 5:00 PM'
                ],
                'statistics' => [
                    'total_patients' => rand(150, 200),
                    'today_appointments' => rand(15, 25),
                    'waiting_queue' => rand(5, 12),
                    'completed_today' => rand(10, 20)
                ],
                'todays_appointments' => $this->generateTodaysAppointments(),
                'queue_status' => $this->getCurrentQueue(),
                'notifications' => [
                    [
                        'type' => 'urgent',
                        'message' => 'Emergency patient in Room 3',
                        'time' => '5 minutes ago'
                    ],
                    [
                        'type' => 'info',
                        'message' => 'New appointment scheduled for 3:00 PM',
                        'time' => '15 minutes ago'
                    ]
                ]
            ]
        ];
    }

    public function updateAppointmentStatus($appointment_id, $status) {
        return [
            'success' => true,
            'message' => 'Appointment status updated',
            'appointment' => [
                'id' => $appointment_id,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function callNextPatient($queue_id = null) {
        return [
            'success' => true,
            'message' => 'Next patient called',
            'patient' => [
                'queue_number' => 'Q087',
                'name' => 'Jane Smith',
                'service' => 'Consultation',
                'wait_time' => '12 minutes'
            ]
        ];
    }

    /**
     * Admin Methods
     */
    public function getAdminDashboard() {
        return [
            'success' => true,
            'data' => [
                'system_stats' => [
                    'total_users' => rand(40, 60),
                    'active_staff' => rand(8, 15),
                    'registered_patients' => rand(1000, 1500),
                    'system_uptime' => '99.' . rand(7, 9) . '%',
                    'daily_appointments' => rand(80, 120),
                    'monthly_consultations' => rand(2000, 3000)
                ],
                'recent_activities' => $this->generateRecentActivities(),
                'staff_status' => $this->getStaffStatus(),
                'system_alerts' => [
                    [
                        'level' => 'info',
                        'message' => 'System backup completed successfully',
                        'time' => '1 hour ago'
                    ],
                    [
                        'level' => 'warning',
                        'message' => 'High queue volume detected',
                        'time' => '2 hours ago'
                    ]
                ]
            ]
        ];
    }

    public function getSystemUsers($role = null) {
        $users = [
            'patients' => $this->generatePatientList(),
            'staff' => $this->generateStaffList(),
            'admins' => $this->generateAdminList()
        ];

        return [
            'success' => true,
            'data' => $role ? $users[$role] : $users
        ];
    }

    /**
     * Helper Methods
     */
    private function generateTodaysAppointments() {
        $appointments = [];
        $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30'];
        $patients = ['John Doe', 'Jane Smith', 'Mike Wilson', 'Sarah Johnson', 'David Brown'];
        $purposes = ['General Consultation', 'Follow-up', 'Medical Certificate', 'Health Screening', 'Vaccination'];
        $statuses = ['Completed', 'In Progress', 'Waiting', 'Scheduled'];

        for ($i = 0; $i < rand(5, 8); $i++) {
            $appointments[] = [
                'id' => $i + 1,
                'time' => $times[array_rand($times)],
                'patient' => $patients[array_rand($patients)],
                'patient_id' => 'STU-2023-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'purpose' => $purposes[array_rand($purposes)],
                'status' => $statuses[array_rand($statuses)]
            ];
        }

        return $appointments;
    }

    private function getCurrentQueue() {
        $queue = [];
        $services = ['Consultation', 'Certificate', 'Check-up', 'Vaccination'];
        $patients = ['Alice Brown', 'Bob Johnson', 'Carol Davis', 'Daniel Wilson', 'Emma Taylor'];

        for ($i = 0; $i < rand(3, 6); $i++) {
            $queue[] = [
                'number' => 'Q' . str_pad(87 + $i, 3, '0', STR_PAD_LEFT),
                'patient' => $patients[array_rand($patients)],
                'service' => $services[array_rand($services)],
                'wait_time' => rand(5, 25) . ' min',
                'priority' => rand(0, 1) ? 'Normal' : 'Urgent'
            ];
        }

        return $queue;
    }

    private function generateRecentActivities() {
        $activities = [
            ['user' => 'Dr. Smith', 'action' => 'Created patient record', 'time' => rand(5, 30) . ' minutes ago'],
            ['user' => 'Nurse Johnson', 'action' => 'Updated appointment status', 'time' => rand(30, 60) . ' minutes ago'],
            ['user' => 'Admin', 'action' => 'System backup completed', 'time' => rand(1, 3) . ' hour ago'],
            ['user' => 'Dr. Brown', 'action' => 'Generated medical certificate', 'time' => rand(2, 5) . ' hours ago'],
            ['user' => 'Receptionist', 'action' => 'Processed queue tickets', 'time' => rand(1, 4) . ' hours ago']
        ];

        return array_slice($activities, 0, rand(3, 5));
    }

    private function getStaffStatus() {
        return [
            ['name' => 'Dr. Sarah Smith', 'role' => 'Doctor', 'status' => 'Active', 'last_login' => rand(1, 5) . ' hours ago'],
            ['name' => 'Dr. Michael Johnson', 'role' => 'Doctor', 'status' => 'Active', 'last_login' => rand(10, 60) . ' minutes ago'],
            ['name' => 'Emily Brown', 'role' => 'Nurse', 'status' => 'Active', 'last_login' => rand(30, 120) . ' minutes ago'],
            ['name' => 'James Wilson', 'role' => 'Receptionist', 'status' => 'Offline', 'last_login' => rand(1, 2) . ' day ago']
        ];
    }

    private function generatePatientList() {
        // Sample patient data
        return [
            ['id' => 1, 'name' => 'John Doe', 'student_id' => 'STU-2023-001', 'email' => 'john.doe@university.edu', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Jane Smith', 'student_id' => 'STU-2023-002', 'email' => 'jane.smith@university.edu', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Mike Wilson', 'student_id' => 'STU-2023-003', 'email' => 'mike.wilson@university.edu', 'status' => 'Inactive']
        ];
    }

    private function generateStaffList() {
        return [
            ['id' => 1, 'name' => 'Dr. Sarah Smith', 'role' => 'Doctor', 'department' => 'General Medicine', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Dr. Michael Johnson', 'role' => 'Doctor', 'department' => 'Cardiology', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Emily Brown', 'role' => 'Nurse', 'department' => 'General', 'status' => 'Active']
        ];
    }

    private function generateAdminList() {
        return [
            ['id' => 1, 'name' => 'System Administrator', 'role' => 'Super Admin', 'permissions' => 'All', 'status' => 'Active'],
            ['id' => 2, 'name' => 'IT Manager', 'role' => 'Tech Admin', 'permissions' => 'System', 'status' => 'Active']
        ];
    }

    /**
     * Real API Methods (for actual Laravel integration)
     */
    private function makeApiCall($endpoint, $method = 'GET', $data = null) {
        $url = $this->base_url . $endpoint;
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                $this->token ? 'Authorization: Bearer ' . $this->token : ''
            ],
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'success' => $http_code >= 200 && $http_code < 300,
            'data' => json_decode($response, true),
            'http_code' => $http_code
        ];
    }
}

// Usage example:
/*
$api = new ClinicAPI();

// Patient login
$login_result = $api->login('john.doe@university.edu', 'password123', 'patient');
if ($login_result['success']) {
    $api = new ClinicAPI($login_result['token']);
    $dashboard = $api->getPatientDashboard($login_result['user']['id']);
}

// Staff operations
$staff_api = new ClinicAPI('staff_token_here');
$staff_dashboard = $staff_api->getStaffDashboard(1);
$appointment_update = $staff_api->updateAppointmentStatus(123, 'completed');

// Admin operations
$admin_api = new ClinicAPI('admin_token_here');
$admin_dashboard = $admin_api->getAdminDashboard();
$all_users = $admin_api->getSystemUsers();
*/
?>
