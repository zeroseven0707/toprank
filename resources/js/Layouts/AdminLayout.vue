<template title="Admin Layout">
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar -->
      <Sidebar />

      <div class="layout-page">
        <!-- Navbar -->
        <Navbar />

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Konten Inertia -->
            <slot />
          </div>

          <!-- Footer -->
          <Footer />
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
  </div>
</template>

<script setup>
import Sidebar from './Partials/Sidebar.vue'
import Navbar from './Partials/Navbar.vue'
import Footer from './Partials/Footer.vue'

defineProps({ title: String })
</script>
