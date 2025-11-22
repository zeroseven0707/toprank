<template>
  <footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
      <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
        <div class="text-body">
          © {{ year }} , dibuat oleh <a href="/" class="footer-link">TopRank</a>
        </div>
        <div class="d-none d-lg-inline-block">
          <a href="/privacy-policy" class="footer-link me-4">Privacy Policy</a>
          <a href="/help" class="footer-link me-4">Help</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear()
</script>
