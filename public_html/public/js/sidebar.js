window.addEventListener('load', function() {
  const currentYear = new Date().getFullYear();

  const yearElement = document.getElementById('currentYear');
  if (yearElement) {
    yearElement.textContent = currentYear;
  }

  const yearFooter = document.getElementById('yearFooter');
  if (yearFooter) {
    yearFooter.textContent = currentYear;
  }

  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarIcon = document.getElementById('sidebarIcon');
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const mainContent = document.getElementById('mainContent');

  function toggleSidebar() {
    if (!sidebar || !mainContent) return;

    sidebar.classList.toggle('show');
    sidebarOverlay?.classList.toggle('show');
    sidebarToggle?.classList.toggle('active');
    mainContent.classList.toggle('shifted');

    if (sidebar.classList.contains('show')) {
      sidebarIcon?.classList.replace('bi-list', 'bi-x');
    } else {
      sidebarIcon?.classList.replace('bi-x', 'bi-list');
    }
  }

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay?.addEventListener('click', toggleSidebar);
  }

  const menuItems = document.querySelectorAll('.sidebar-nav ul li a');
  menuItems.forEach(item => {
    item.addEventListener('click', function() {
      menuItems.forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      if (window.innerWidth < 992) {
        sidebar?.classList.remove('show');
        sidebarOverlay?.classList.remove('show');
        sidebarToggle?.classList.remove('active');
        mainContent?.classList.remove('shifted');
        sidebarIcon?.classList.replace('bi-x', 'bi-list');
      }
    });
  });

  window.addEventListener('resize', function() {
    if (window.innerWidth < 992 && mainContent?.classList.contains('shifted')) {
      mainContent.classList.remove('shifted');
    }
  });
});
