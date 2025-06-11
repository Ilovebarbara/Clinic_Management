<!-- Quick Navigation Component -->
<div id="quickNav" class="quick-nav">
    <div class="quick-nav-toggle" onclick="toggleQuickNav()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="quick-nav-menu">
        <div class="quick-nav-header">
            <h4>Quick Navigation</h4>
            <button class="nav-close" onclick="toggleQuickNav()">×</button>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Web Interfaces</div>
            <a href="patient-portal.php" class="nav-link">
                <i class="fas fa-user-injured"></i> Patient Portal
            </a>
            <a href="staff-dashboard.php" class="nav-link">
                <i class="fas fa-user-md"></i> Staff Dashboard
            </a>
            <a href="admin-panel.php" class="nav-link">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Core System</div>
            <a href="login.php" class="nav-link">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="patients.php" class="nav-link">
                <i class="fas fa-users"></i> Patients
            </a>
            <a href="appointments.php" class="nav-link">
                <i class="fas fa-calendar-check"></i> Appointments
            </a>
        </div>        <div class="nav-section">
            <div class="nav-section-title">Specialized</div>
            <a href="kiosk.php" class="nav-link">
                <i class="fas fa-desktop"></i> Kiosk
            </a>
            <a href="mobile.php" class="nav-link">
                <i class="fas fa-mobile-alt"></i> Mobile
            </a>
            <a href="queue.php" class="nav-link">
                <i class="fas fa-list-ol"></i> Queue
            </a>
            <a href="enhanced-queue.php" class="nav-link">
                <i class="fas fa-rocket"></i> Enhanced Queue
            </a>
            <a href="analytics-dashboard.php" class="nav-link">
                <i class="fas fa-chart-line"></i> Analytics
            </a>
            <a href="medical-records.php" class="nav-link">
                <i class="fas fa-file-medical"></i> Records
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Overview</div>
            <a href="web-views.php" class="nav-link">
                <i class="fas fa-home"></i> All Interfaces
            </a>
        </div>
    </div>
</div>

<style>
.quick-nav {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

.quick-nav-toggle {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.2rem;
}

.quick-nav-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.quick-nav-menu {
    position: absolute;
    top: 60px;
    right: 0;
    width: 250px;
    max-height: 500px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 1rem;
    transform: translateX(100%) scale(0.8);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    overflow-y: auto;
}

.quick-nav-menu.active {
    transform: translateX(0) scale(1);
    opacity: 1;
    visibility: visible;
}

.quick-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.quick-nav-header h4 {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.nav-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-section {
    margin-bottom: 1rem;
}

.nav-section-title {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
}

.nav-link i {
    width: 16px;
    color: #F79489;
}

@media (max-width: 768px) {
    .quick-nav {
        top: 10px;
        right: 10px;
    }
    
    .quick-nav-menu {
        width: 200px;
        max-height: 400px;
    }
}
</style>

<script>
function toggleQuickNav() {
    const menu = document.querySelector('.quick-nav-menu');
    menu.classList.toggle('active');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const quickNav = document.getElementById('quickNav');
    if (!quickNav.contains(event.target)) {
        document.querySelector('.quick-nav-menu').classList.remove('active');
    }
});

// Highlight current page
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.style.background = 'rgba(247, 148, 137, 0.3)';
            link.style.color = 'white';
        }
    });
});
</script>
