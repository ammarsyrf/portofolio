<?php
/**
 * Admin Layout: Footer
 */
?>
  </main>
</div>
</div>

<script>
  // Mobile sidebar toggle
  const sidebarToggle = document.getElementById('sidebarToggle');
  const adminSidebar = document.getElementById('adminSidebar');
  if (sidebarToggle && adminSidebar) {
    sidebarToggle.addEventListener('click', () => {
      adminSidebar.classList.toggle('open');
    });
  }
</script>
</body>
</html>
