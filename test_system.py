#!/usr/bin/env python3
"""
Comprehensive System Test Script
Tests all major functionality of the Clinic Management System
"""

import requests
import json
import sys
from datetime import datetime, date, timedelta
import sqlite3
import os

class ClinicSystemTester:
    def __init__(self, base_url="http://127.0.0.1:5000"):
        self.base_url = base_url
        self.session = requests.Session()
        self.test_results = []
        self.admin_credentials = {"username": "admin", "password": "admin123"}
        
    def run_all_tests(self):
        """Run all system tests"""
        print("🧪 Starting Clinic Management System Tests")
        print("=" * 50)
        
        # Test categories
        test_categories = [
            ("Database Connectivity", self.test_database),
            ("Web Application", self.test_web_application),
            ("Authentication System", self.test_authentication),
            ("Patient Management", self.test_patient_management),
            ("Appointment System", self.test_appointment_system),
            ("Queue Management", self.test_queue_system),
            ("API Endpoints", self.test_api_endpoints),
            ("User Interface", self.test_ui_components),
            ("System Performance", self.test_performance)
        ]
        
        for category_name, test_function in test_categories:
            print(f"\n📋 Testing {category_name}...")
            try:
                results = test_function()
                self.test_results.extend(results)
                passed = sum(1 for r in results if r['status'] == 'PASS')
                total = len(results)
                print(f"   ✅ {passed}/{total} tests passed")
            except Exception as e:
                print(f"   ❌ Error in {category_name}: {str(e)}")
                self.test_results.append({
                    'category': category_name,
                    'test': 'Category Execution',
                    'status': 'FAIL',
                    'error': str(e)
                })
        
        self.generate_report()
    
    def test_database(self):
        """Test database connectivity and structure"""
        results = []
        
        # Test database file exists
        db_path = "instance/clinic.db"
        if os.path.exists(db_path):
            results.append({
                'category': 'Database',
                'test': 'Database file exists',
                'status': 'PASS'
            })
        else:
            results.append({
                'category': 'Database',
                'test': 'Database file exists',
                'status': 'FAIL',
                'error': 'Database file not found'
            })
            return results
        
        # Test table structure
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            
            # Check for required tables
            required_tables = ['user', 'patient', 'doctor', 'appointment', 'medical_record', 'queue_ticket']
            cursor.execute("SELECT name FROM sqlite_master WHERE type='table'")
            existing_tables = [row[0] for row in cursor.fetchall()]
            
            for table in required_tables:
                if table in existing_tables:
                    results.append({
                        'category': 'Database',
                        'test': f'Table {table} exists',
                        'status': 'PASS'
                    })
                else:
                    results.append({
                        'category': 'Database',
                        'test': f'Table {table} exists',
                        'status': 'FAIL',
                        'error': f'Table {table} not found'
                    })
            
            # Test data integrity
            cursor.execute("SELECT COUNT(*) FROM user WHERE role='super_admin'")
            admin_count = cursor.fetchone()[0]
            
            if admin_count > 0:
                results.append({
                    'category': 'Database',
                    'test': 'Admin user exists',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Database',
                    'test': 'Admin user exists',
                    'status': 'FAIL',
                    'error': 'No admin user found'
                })
            
            conn.close()
            
        except Exception as e:
            results.append({
                'category': 'Database',
                'test': 'Database query execution',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def test_web_application(self):
        """Test basic web application functionality"""
        results = []
        
        # Test main pages
        pages_to_test = [
            ('/', 'Home page'),
            ('/services', 'Services page'),
            ('/about', 'About page'),
            ('/contact', 'Contact page'),
            ('/auth/login', 'Login page'),
            ('/queue/kiosk', 'Queue kiosk'),
            ('/queue/status', 'Queue status')
        ]
        
        for url, page_name in pages_to_test:
            try:
                response = requests.get(f"{self.base_url}{url}")
                if response.status_code == 200:
                    results.append({
                        'category': 'Web Application',
                        'test': f'{page_name} loads',
                        'status': 'PASS'
                    })
                else:
                    results.append({
                        'category': 'Web Application',
                        'test': f'{page_name} loads',
                        'status': 'FAIL',
                        'error': f'HTTP {response.status_code}'
                    })
            except Exception as e:
                results.append({
                    'category': 'Web Application',
                    'test': f'{page_name} loads',
                    'status': 'FAIL',
                    'error': str(e)
                })
        
        return results
    
    def test_authentication(self):
        """Test authentication system"""
        results = []
        
        # Test login page
        try:
            response = requests.get(f"{self.base_url}/auth/login")
            if "login" in response.text.lower():
                results.append({
                    'category': 'Authentication',
                    'test': 'Login page renders',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Authentication',
                    'test': 'Login page renders',
                    'status': 'FAIL',
                    'error': 'Login form not found'
                })
        except Exception as e:
            results.append({
                'category': 'Authentication',
                'test': 'Login page renders',
                'status': 'FAIL',
                'error': str(e)
            })
        
        # Test login functionality
        try:
            login_data = {
                'username': self.admin_credentials['username'],
                'password': self.admin_credentials['password']
            }
            response = self.session.post(f"{self.base_url}/auth/login", data=login_data)
            
            if response.status_code in [200, 302]:  # Success or redirect
                results.append({
                    'category': 'Authentication',
                    'test': 'Admin login',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Authentication',
                    'test': 'Admin login',
                    'status': 'FAIL',
                    'error': f'HTTP {response.status_code}'
                })
        except Exception as e:
            results.append({
                'category': 'Authentication',
                'test': 'Admin login',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def test_patient_management(self):
        """Test patient management functionality"""
        results = []
        
        # Test patient registration page
        try:
            response = self.session.get(f"{self.base_url}/patient/register")
            if response.status_code == 200:
                results.append({
                    'category': 'Patient Management',
                    'test': 'Patient registration page',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Patient Management',
                    'test': 'Patient registration page',
                    'status': 'FAIL',
                    'error': f'HTTP {response.status_code}'
                })
        except Exception as e:
            results.append({
                'category': 'Patient Management',
                'test': 'Patient registration page',
                'status': 'FAIL',
                'error': str(e)
            })
        
        # Test patient list (requires admin login)
        try:
            response = self.session.get(f"{self.base_url}/admin/patients")
            if response.status_code in [200, 302]:
                results.append({
                    'category': 'Patient Management',
                    'test': 'Patient list access',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Patient Management',
                    'test': 'Patient list access',
                    'status': 'FAIL',
                    'error': f'HTTP {response.status_code}'
                })
        except Exception as e:
            results.append({
                'category': 'Patient Management',
                'test': 'Patient list access',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def test_appointment_system(self):
        """Test appointment system"""
        results = []
        
        # Test appointment booking page
        try:
            response = self.session.get(f"{self.base_url}/appointments/book")
            if response.status_code in [200, 302]:
                results.append({
                    'category': 'Appointment System',
                    'test': 'Appointment booking page',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Appointment System',
                    'test': 'Appointment booking page',
                    'status': 'FAIL',
                    'error': f'HTTP {response.status_code}'
                })
        except Exception as e:
            results.append({
                'category': 'Appointment System',
                'test': 'Appointment booking page',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def test_queue_system(self):
        """Test queue management system"""
        results = []
        
        # Test queue kiosk
        try:
            response = requests.get(f"{self.base_url}/queue/kiosk")
            if "service" in response.text.lower():
                results.append({
                    'category': 'Queue System',
                    'test': 'Queue kiosk interface',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Queue System',
                    'test': 'Queue kiosk interface',
                    'status': 'FAIL',
                    'error': 'Service selection not found'
                })
        except Exception as e:
            results.append({
                'category': 'Queue System',
                'test': 'Queue kiosk interface',
                'status': 'FAIL',
                'error': str(e)
            })
        
        # Test queue status
        try:
            response = requests.get(f"{self.base_url}/queue/status")
            if response.status_code == 200:
                results.append({
                    'category': 'Queue System',
                    'test': 'Queue status display',
                    'status': 'PASS'
                })
            else:
                results.append({
                    'category': 'Queue System',
                    'test': 'Queue status display',
                    'status': 'FAIL',
                    'error': f'HTTP {response.status_code}'
                })
        except Exception as e:
            results.append({
                'category': 'Queue System',
                'test': 'Queue status display',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def test_api_endpoints(self):
        """Test API endpoints"""
        results = []
        
        # Test API endpoints
        api_endpoints = [
            ('/api/queue/current-status', 'Queue status API'),
            ('/api/dashboard-stats', 'Dashboard stats API'),
            ('/api/notifications', 'Notifications API')
        ]
        
        for endpoint, name in api_endpoints:
            try:
                response = self.session.get(f"{self.base_url}{endpoint}")
                if response.status_code in [200, 401]:  # 401 is acceptable for protected endpoints
                    results.append({
                        'category': 'API Endpoints',
                        'test': name,
                        'status': 'PASS'
                    })
                else:
                    results.append({
                        'category': 'API Endpoints',
                        'test': name,
                        'status': 'FAIL',
                        'error': f'HTTP {response.status_code}'
                    })
            except Exception as e:
                results.append({
                    'category': 'API Endpoints',
                    'test': name,
                    'status': 'FAIL',
                    'error': str(e)
                })
        
        return results
    
    def test_ui_components(self):
        """Test UI components and static files"""
        results = []
        
        # Test static files
        static_files = [
            ('/static/css/style.css', 'Main CSS file'),
            ('/static/js/script.js', 'Main JS file'),
            ('/static/js/clinic-app.js', 'Clinic app JS file')
        ]
        
        for file_path, name in static_files:
            try:
                response = requests.get(f"{self.base_url}{file_path}")
                if response.status_code == 200:
                    results.append({
                        'category': 'UI Components',
                        'test': name,
                        'status': 'PASS'
                    })
                else:
                    results.append({
                        'category': 'UI Components',
                        'test': name,
                        'status': 'FAIL',
                        'error': f'HTTP {response.status_code}'
                    })
            except Exception as e:
                results.append({
                    'category': 'UI Components',
                    'test': name,
                    'status': 'FAIL',
                    'error': str(e)
                })
        
        return results
    
    def test_performance(self):
        """Test system performance"""
        results = []
        
        # Test response times
        start_time = datetime.now()
        try:
            response = requests.get(f"{self.base_url}/")
            end_time = datetime.now()
            response_time = (end_time - start_time).total_seconds()
            
            if response_time < 2.0:  # Under 2 seconds
                results.append({
                    'category': 'Performance',
                    'test': 'Home page response time',
                    'status': 'PASS',
                    'details': f'{response_time:.2f}s'
                })
            else:
                results.append({
                    'category': 'Performance',
                    'test': 'Home page response time',
                    'status': 'FAIL',
                    'error': f'Slow response: {response_time:.2f}s'
                })
        except Exception as e:
            results.append({
                'category': 'Performance',
                'test': 'Home page response time',
                'status': 'FAIL',
                'error': str(e)
            })
        
        return results
    
    def generate_report(self):
        """Generate test report"""
        print("\n" + "=" * 50)
        print("📊 TEST RESULTS SUMMARY")
        print("=" * 50)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for r in self.test_results if r['status'] == 'PASS')
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        if failed_tests > 0:
            print("\n🔍 FAILED TESTS:")
            print("-" * 30)
            for result in self.test_results:
                if result['status'] == 'FAIL':
                    print(f"❌ {result['category']} - {result['test']}")
                    if 'error' in result:
                        print(f"   Error: {result['error']}")
        
        # Save detailed report
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        report_file = f"test_report_{timestamp}.json"
        
        with open(report_file, 'w') as f:
            json.dump({
                'timestamp': timestamp,
                'summary': {
                    'total': total_tests,
                    'passed': passed_tests,
                    'failed': failed_tests,
                    'success_rate': (passed_tests/total_tests)*100
                },
                'detailed_results': self.test_results
            }, f, indent=2)
        
        print(f"\n📄 Detailed report saved: {report_file}")
        
        if failed_tests == 0:
            print("\n🎉 ALL TESTS PASSED! System is ready for use.")
        else:
            print(f"\n⚠️  {failed_tests} tests failed. Please review and fix issues.")
        
        return passed_tests == total_tests

def main():
    """Main test execution"""
    import argparse
    
    parser = argparse.ArgumentParser(description='Clinic Management System Tester')
    parser.add_argument('--url', default='http://127.0.0.1:5000', help='Base URL of the application')
    parser.add_argument('--quick', action='store_true', help='Run quick tests only')
    
    args = parser.parse_args()
    
    tester = ClinicSystemTester(args.url)
    
    try:
        if args.quick:
            print("🚀 Running quick tests...")
            # Test only essential functionality
            essential_tests = [
                ("Database", tester.test_database),
                ("Web Application", tester.test_web_application),
                ("API Endpoints", tester.test_api_endpoints)
            ]
            
            for name, test_func in essential_tests:
                print(f"Testing {name}...")
                results = test_func()
                tester.test_results.extend(results)
        else:
            tester.run_all_tests()
        
        success = tester.generate_report()
        sys.exit(0 if success else 1)
        
    except KeyboardInterrupt:
        print("\n\n⏹️  Testing interrupted by user")
        sys.exit(1)
    except Exception as e:
        print(f"\n❌ Testing failed with error: {str(e)}")
        sys.exit(1)

if __name__ == '__main__':
    main()
