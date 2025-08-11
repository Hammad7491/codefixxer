<!-- === Minimal sidebar styles (keep or merge with your CSS) === -->
<style>
  .sidebar { width: 280px; background: #fff; border-right: 1px solid #eee; }
  .sidebar-logo { display:flex; align-items:center; gap:.5rem; padding:1rem 1.25rem; }
  .sidebar-menu { list-style:none; margin:0; padding: .5rem 1rem 1rem; }
  .sidebar-menu-group-title { padding: .75rem 1rem; font-size: .85rem; text-transform: uppercase; color:#6b7280; }
  .sidebar-menu > li > a { display:flex; align-items:center; gap:.6rem; padding:.65rem .9rem; border-radius:.75rem; text-decoration:none; color:#111827; cursor: pointer; }
  .sidebar-menu > li > a:hover { background:#f3f4f6; }
  .sidebar-menu > li > a.active { background:#4f7cff; color:#fff; }
  .sidebar-submenu { list-style:none; margin: .25rem 0 0 0; padding: .4rem .4rem .6rem 2.15rem; border-left: 2px solid #e5e7eb; border-radius: .25rem; display: none; }
  .sidebar-submenu.is-open { display: block; }
  .sidebar-submenu li a { display:flex; align-items:center; gap:.5rem; padding:.45rem .6rem; border-radius:.5rem; text-decoration:none; color:#1f2937; background: #f8fafc; margin:.25rem 0; }
  .sidebar-submenu li a:hover { background:#eef2ff; }
  .sidebar-submenu li a.active { background:#dbe5ff; font-weight:600; }
  .circle-icon { font-size: .6rem; }
</style>

<aside class="sidebar">
  <button type="button" class="sidebar-close-btn btn btn-sm btn-light ms-2 mt-2">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>

  <div>
    <a href="index.html" class="sidebar-logo">
      <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo" height="32">
      <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo d-none" height="32">
      <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon" height="28">
    </a>
  </div>

  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-accordion">

      <!-- Dashboard -->
      <li class="nav-item">
        <a class="d-flex align-items-center js-toggle">
          <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
          <span>Dashboard</span>
          <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="index.html">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> AI
            </a>
          </li>
        </ul>
      </li>

      <!-- Users section -->
      <li class="sidebar-menu-group-title">Users</li>

      <!-- User Management -->
      <li class="nav-item">
        <a class="d-flex align-items-center js-toggle">
          <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
          <span>User Management</span>
          <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="{{ route('admin.users.create') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Users
            </a>
          </li>
          <li>
            <a href="{{ route('admin.users.index') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Users List
            </a>
          </li>
          <li>
            <a href="{{ route('admin.roles.index') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Roles & Permissions
            </a>
          </li>
          <li>
            <a href="{{ route('admin.permissions.index') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Permissions
            </a>
          </li>
        </ul>
      </li>

      <!-- CV Management section -->
      <li class="sidebar-menu-group-title">CV Management</li>

      <!-- Skills -->
      <li class="nav-item">
        <a class="d-flex align-items-center js-toggle">
          <iconify-icon icon="mdi:tools" class="menu-icon"></iconify-icon>
          <span>Skills</span>
          <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="{{ route('admin.skills.create') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Skill
            </a>
          </li>
          <li>
            <a href="{{ route('admin.skills.index') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Skills
            </a>
          </li>
        </ul>
      </li>


            <!-- Jobs -->
<li class="nav-item">
  <a class="d-flex align-items-center js-toggle">
    <iconify-icon icon="mdi:briefcase-outline" class="menu-icon"></iconify-icon>
    <span>Jobs</span>
    <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
  </a>
  <ul class="sidebar-submenu">
    <li>
      <a href="{{ route('admin.jobs.create') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Job
      </a>
    </li>
    <li>
      <a href="">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Jobs
      </a>
    </li>
  </ul>
</li>


<!-- Projects -->
<li class="nav-item">
  <a class="d-flex align-items-center js-toggle">
    <iconify-icon icon="mdi:folder-outline" class="menu-icon"></iconify-icon>
    <span>Projects</span>
    <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
  </a>
  <ul class="sidebar-submenu">
    <li>
      <a href="{{ route('admin.projects.create') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Project
      </a>
    </li>
    <li>
      <a href="{{ route('admin.projects.index') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Projects
      </a>
    </li>
  </ul>
</li>




<!-- Education -->
<li class="nav-item">
  <a class="d-flex align-items-center js-toggle">
    <iconify-icon icon="mdi:school-outline" class="menu-icon"></iconify-icon>
    <span>Education</span>
    <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
  </a>
  <ul class="sidebar-submenu">
    <li>
      <a href="{{ route('admin.educations.create') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Education
      </a>
    </li>
    <li>
      <a href="{{ route('admin.educations.index') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Education
      </a>
    </li>
  </ul>
</li>



  <!-- Languages (NEW) -->
      <li class="nav-item">
        <a class="d-flex align-items-center js-toggle">
          <iconify-icon icon="mdi:translate" class="menu-icon"></iconify-icon>
          <span>Languages</span>
          <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="{{ route('admin.languages.create') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Language
            </a>
          </li>
          <li>
            <a href="{{ route('admin.languages.index') }}">
              <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Languages
            </a>
          </li>
        </ul>
      </li>



      <!-- Legal Documents section -->
<li class="sidebar-menu-group-title">Legal Documents</li>

<!-- Invoice -->
<li class="nav-item">
  <a class="d-flex align-items-center js-toggle">
    <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
    <span>Invoice</span>
    <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
  </a>
  <ul class="sidebar-submenu">
    <li>
      <a href="{{ route('admin.invoices.create') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Invoice
      </a>
    </li>
    <li>
      <a href="{{ route('admin.invoices.index') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Invoices
      </a>
    </li>
  </ul>
</li>



<li class="nav-item">
  <a class="d-flex align-items-center js-toggle">
    <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
    <span>Contract</span>
    <span class="ms-auto"><iconify-icon icon="mdi:chevron-down"></iconify-icon></span>
  </a>
  <ul class="sidebar-submenu">
    <li>
      <a href="{{ route('admin.contracts.create') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> Add Contract
      </a>
    </li>
    <li>
      <a href="{{ route('admin.contracts.index') }}">
        <i class="ri-circle-fill circle-icon text-primary w-auto"></i> List Contracts
      </a>
    </li>
  </ul>
</li>

    

      <!-- Email -->
      <li class="nav-item">
        <a href="email.html" class="d-flex align-items-center">
          <iconify-icon icon="mage:email" class="menu-icon"></iconify-icon>
          <span>Email</span>
        </a>
      </li>





    </ul>
  </div>
</aside>

<!-- === Tiny vanilla JS: dropdown toggle + accordion + active submenu === -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const accordion = document.getElementById('sidebar-accordion');

    // Toggle dropdowns
    accordion.querySelectorAll('.js-toggle').forEach(function (toggle) {
      toggle.addEventListener('click', function (e) {
        e.preventDefault();

        const currentSub = this.nextElementSibling;
        if (!currentSub || !currentSub.classList.contains('sidebar-submenu')) return;

        // Close others (accordion)
        accordion.querySelectorAll('.sidebar-submenu.is-open').forEach(function (open) {
          if (open !== currentSub) open.classList.remove('is-open');
        });
        accordion.querySelectorAll('.js-toggle.active').forEach(function (btn) {
          if (btn !== toggle) btn.classList.remove('active');
        });

        // Toggle current
        currentSub.classList.toggle('is-open');
        this.classList.toggle('active', currentSub.classList.contains('is-open'));
      });
    });

    // Submenu active link (no persistence across pages)
    const submenuLinks = accordion.querySelectorAll('.sidebar-submenu a');
    submenuLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        submenuLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });
  });
</script>
