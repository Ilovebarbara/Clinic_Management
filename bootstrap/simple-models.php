<?php

// Simple User model for our basic Laravel
class User extends BaseModel {
    protected static $table = 'users';
    protected static $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 
        'role', 'phone', 'address', 'is_active', 'avatar'
    ];
    
    public static function authenticate($email, $password) {
        $db = getDbConnection();
        if (!$db) return null;
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    public static function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $user = new static($data);
        return $user->save() ? $user : null;
    }
}

// Simple Patient model
class Patient extends BaseModel {
    protected static $table = 'patients';
    protected static $fillable = [
        'student_employee_no', 'first_name', 'middle_name', 'last_name',
        'email', 'phone', 'address', 'age', 'sex', 'campus', 'college_office',
        'course_year_designation', 'emergency_contact_name', 'emergency_contact_relation',
        'emergency_contact_phone', 'blood_type', 'allergies', 'user_id'
    ];
    
    public function getFullName() {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }
    
    public static function findByStudentNo($student_no) {
        $db = getDbConnection();
        if (!$db) return null;
        
        $stmt = $db->prepare("SELECT * FROM patients WHERE student_employee_no = ?");
        $stmt->execute([$student_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Simple Appointment model
class Appointment extends BaseModel {
    protected static $table = 'appointments';
    protected static $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'appointment_time',
        'purpose', 'status', 'notes', 'created_by'
    ];
    
    public static function getToday() {
        $db = getDbConnection();
        if (!$db) return [];
        
        $stmt = $db->prepare("SELECT a.*, p.first_name, p.last_name, p.student_employee_no, u.first_name as doctor_first_name, u.last_name as doctor_last_name 
                             FROM appointments a 
                             JOIN patients p ON a.patient_id = p.id 
                             JOIN users u ON a.doctor_id = u.id 
                             WHERE DATE(a.appointment_date) = CURDATE()
                             ORDER BY a.appointment_time");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Simple QueueTicket model
class QueueTicket extends BaseModel {
    protected static $table = 'queue_tickets';
    protected static $fillable = [
        'patient_id', 'queue_number', 'service_type', 'priority', 'status',
        'created_at', 'called_at', 'completed_at', 'window_number'
    ];
    
    public static function generateNumber($service_type) {
        $prefix = strtoupper(substr($service_type, 0, 1));
        $number = rand(100, 999);
        return $prefix . $number;
    }
    
    public static function getCurrentQueue() {
        $db = getDbConnection();
        if (!$db) return [];
        
        $stmt = $db->prepare("SELECT q.*, p.first_name, p.last_name 
                             FROM queue_tickets q 
                             JOIN patients p ON q.patient_id = p.id 
                             WHERE q.status IN ('waiting', 'called') 
                             ORDER BY q.priority DESC, q.created_at ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Simple MedicalRecord model
class MedicalRecord extends BaseModel {
    protected static $table = 'medical_records';
    protected static $fillable = [
        'patient_id', 'doctor_id', 'transaction_type', 'date_time',
        'height', 'weight', 'heart_rate', 'respiratory_rate', 'temperature',
        'blood_pressure', 'pain_scale', 'symptoms', 'diagnosis', 'treatment',
        'notes', 'created_by'
    ];
    
    public static function getPatientHistory($patient_id) {
        $db = getDbConnection();
        if (!$db) return [];
        
        $stmt = $db->prepare("SELECT m.*, u.first_name as doctor_first_name, u.last_name as doctor_last_name 
                             FROM medical_records m 
                             JOIN users u ON m.doctor_id = u.id 
                             WHERE m.patient_id = ? 
                             ORDER BY m.date_time DESC");
        $stmt->execute([$patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

echo "✅ Simple models loaded successfully!\n";

?>
