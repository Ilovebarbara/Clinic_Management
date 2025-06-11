<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - University Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating Background Orbs */
        .floating-orb {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(247,148,137,0.2), rgba(248,175,166,0.1));
            animation: floatOrb 6s ease-in-out infinite;
            pointer-events: none;
        }

        .floating-orb:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-orb:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-orb:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(30px, -30px) rotate(90deg) scale(1.1); }
            50% { transform: translate(-20px, 20px) rotate(180deg) scale(0.9); }
            75% { transform: translate(-30px, -10px) rotate(270deg) scale(1.05); }
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(247,148,137,0.2);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: heartbeat 2s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-link {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #F79489;
            background: rgba(247,148,137,0.1);
        }

        /* Main Content */
        .main-content {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        /* Registration Form */
        .registration-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 3rem;
            animation: slideInUp 0.8s ease;
        }

        .registration-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .registration-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .registration-subtitle {
            color: #666;
            font-size: 1.1rem;
            font-weight: 400;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #F79489;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .required {
            color: #F79489;
        }

        .form-input, .form-select, .form-textarea {
            padding: 0.875rem 1rem;
            border: 2px solid rgba(247,148,137,0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 3px rgba(247,148,137,0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }

        /* Submit Button */
        .submit-section {
            text-align: center;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(247,148,137,0.2);
        }

        .submit-btn {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(247, 148, 137, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(247, 148, 137, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }

            .main-content {
                padding: 0 1rem;
            }

            .registration-card {
                padding: 2rem 1.5rem;
            }

            .registration-title {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Orbs -->
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo">
                <i class="fas fa-heartbeat"></i>
                <span class="logo-text">University Clinic</span>
            </a>

            <nav class="header-nav">
                <a href="/login" class="nav-link">Login</a>
                <a href="/kiosk" class="nav-link">Kiosk</a>
                <a href="/queue" class="nav-link">Queue Status</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="registration-card">
            <div class="registration-header">
                <h1 class="registration-title">Patient Registration</h1>
                <p class="registration-subtitle">Please fill in all required information to register</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register">
                <!-- Personal Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Personal Information
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Student/Employee No. <span class="required">*</span></label>
                            <input type="text" name="student_no" class="form-input" required 
                                   placeholder="e.g., STU-2024-001">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address <span class="required">*</span></label>
                            <input type="email" name="email" class="form-input" required 
                                   placeholder="your.email@university.edu">
                        </div>

                        <div class="form-group">
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" class="form-input" required 
                                   placeholder="Juan">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-input" 
                                   placeholder="Santos">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-input" required 
                                   placeholder="Dela Cruz">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-input" required 
                                   placeholder="+63 912 345 6789">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-input" min="1" max="120" 
                                   placeholder="21">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-select">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Complete Address</label>
                            <textarea name="address" class="form-textarea" 
                                      placeholder="Lot No./Street, Barangay, City/Municipality, Province"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Academic/Work Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Academic/Work Information
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Campus</label>
                            <select name="campus" class="form-select">
                                <option value="">Select Campus</option>
                                <option value="Malolos">Malolos</option>
                                <option value="Meneses">Meneses</option>
                                <option value="Hagonoy">Hagonoy</option>
                                <option value="Bocaue">Bocaue</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">College/Office</label>
                            <select name="college_office" class="form-select">
                                <option value="">Select College/Office</option>
                                <option value="CICT">CICT - College of Information and Communications Technology</option>
                                <option value="CON">CON - College of Nursing</option>
                                <option value="COE">COE - College of Engineering</option>
                                <option value="CBA">CBA - College of Business Administration</option>
                                <option value="CAS">CAS - College of Arts and Sciences</option>
                                <option value="CHTM">CHTM - College of Hospitality and Tourism Management</option>
                                <option value="HR">HR - Human Resources</option>
                                <option value="Accounting">Accounting</option>
                                <option value="Registrar">Registrar</option>
                                <option value="Library">Library</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Course & Year / Designation</label>
                            <input type="text" name="course_year" class="form-input" 
                                   placeholder="e.g., BSIT 3rd Year or Administrative Assistant II">
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-phone-alt"></i>
                        Emergency Contact
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Contact Name</label>
                            <input type="text" name="emergency_contact" class="form-input" 
                                   placeholder="Maria Dela Cruz">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Relationship</label>
                            <select name="emergency_relation" class="form-select">
                                <option value="">Select Relationship</option>
                                <option value="Mother">Mother</option>
                                <option value="Father">Father</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Guardian">Guardian</option>
                                <option value="Friend">Friend</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="emergency_phone" class="form-input" 
                                   placeholder="+63 912 345 6789">
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-stethoscope"></i>
                        Medical Information
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Blood Type</label>
                            <select name="blood_type" class="form-select">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Known Allergies</label>
                            <textarea name="allergies" class="form-textarea" 
                                      placeholder="List any known allergies to medications, food, or other substances. Write 'None' if no known allergies."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="submit-section">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-user-plus"></i>
                        Register Patient Account
                    </button>
                    <p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">
                        Already have an account? <a href="/login" style="color: #F79489; text-decoration: none; font-weight: 500;">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-format phone numbers
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.startsWith('63')) {
                        value = '+63 ' + value.slice(2, 5) + ' ' + value.slice(5, 8) + ' ' + value.slice(8, 12);
                    } else if (value.startsWith('09')) {
                        value = '+63 9' + value.slice(2, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7, 11);
                    }
                    e.target.value = value;
                });
            });

            // Student number validation
            const studentNoInput = document.querySelector('input[name="student_no"]');
            if (studentNoInput) {
                studentNoInput.addEventListener('blur', function(e) {
                    const value = e.target.value.toUpperCase();
                    if (value && !value.match(/^(STU|FAC|STF)-\d{4}-\d{3}$/)) {
                        e.target.setCustomValidity('Please use format: STU-2024-001 (for students), FAC-2024-001 (for faculty), or STF-2024-001 (for staff)');
                    } else {
                        e.target.setCustomValidity('');
                    }
                });
            }

            // Form submission enhancement
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const submitBtn = document.querySelector('.submit-btn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
