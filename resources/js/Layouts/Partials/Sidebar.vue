<template>
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme d-flex flex-column">
    <!-- Logo / Brand -->
    <div class="app-brand demo">
      <Link href="/" class="app-brand-link">
        <span class="app-brand-logo">
            <img src="/assets/img/icons/mappy-trends.png" alt="Trends Logo" class="w-10 h-10" />
        </span>
        <span class="app-brand-text demo menu-text fw-bold">MTrends</span>
      </Link>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" @click.prevent="toggleSidebar">
        <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- ✅ Menu Dinamis & Berdasarkan Permission -->
    <ul class="menu-inner py-1">
      <template v-for="(item, index) in visibleMenu" :key="index">
        <!-- Header -->
        <li
          v-if="item.header"
          class="menu-header small text-uppercase text-muted"
        >
          <span class="menu-header-text">{{ item.header }}</span>
        </li>

        <!-- Menu dengan Submenu -->
        <li
          v-else-if="item.children?.length"
          class="menu-item"
          :class="{ open: isOpen(item) }"
        >
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </a>

          <ul class="menu-sub">
            <li
              v-for="(child, i) in item.children"
              :key="i"
              class="menu-item"
              :class="{ active: isActive(child.route) }"
            >
              <Link :href="child.route" class="menu-link">
                <div>{{ child.title }}</div>
              </Link>
            </li>
          </ul>
        </li>

        <!-- Menu Tunggal -->
        <li
          v-else
          class="menu-item"
          :class="{ active: isActive(item.route) }"
        >
          <Link :href="item.route" class="menu-link">
            <i class="menu-icon tf-icons" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </Link>
        </li>
      </template>
    </ul>
  </aside>
</template>

<script setup>
import menu from "@/config/menu";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const userPermissions = page.props.auth?.permissions || [];

// 🔐 Fungsi cek izin
const can = (perm) => {
  if (!perm) return true;
  const normalized = userPermissions.map((p) => p.toLowerCase());
  return normalized.includes(perm.toLowerCase());
};

// 🎯 Filter menu berdasarkan izin
const visibleMenu = computed(() => {
  const filtered = [];

  for (let i = 0; i < menu.length; i++) {
    const item = menu[i];

    // 🔹 Jika ini header
    if (item.header) {
      let hasVisibleBelow = false;

      // cari menu setelah header ini sampai header berikutnya
      for (let j = i + 1; j < menu.length; j++) {
        const next = menu[j];
        if (next.header) break; // stop kalau header baru
        if (next.children?.length) {
          const visibleChildren = next.children.filter(
            (child) => !child.permission || can(child.permission)
          );
          if (visibleChildren.length) {
            hasVisibleBelow = true;
            break;
          }
        } else if (!next.permission || can(next.permission)) {
          hasVisibleBelow = true;
          break;
        }
      }

      // hanya tambahkan header kalau ada menu visible di bawahnya
      if (hasVisibleBelow) filtered.push(item);
    }

    // 🔹 Jika punya submenu
    else if (item.children?.length) {
      const visibleChildren = item.children.filter(
        (child) => !child.permission || can(child.permission)
      );
      if (visibleChildren.length) filtered.push({ ...item, children: visibleChildren });
    }

    // 🔹 Item tunggal
    else if (!item.permission || can(item.permission)) {
      filtered.push(item);
    }
  }

  return filtered;
});

// 📂 Menu aktif / terbuka (reactive)
const currentPath = computed(() => (page.url || '').split('?')[0].replace(/\/+$/, ''));
const segmentMatch = (route) => {
  const base = String(route || '').split('?')[0].replace(/\/+$/, '');
  return currentPath.value === base || currentPath.value.startsWith(base + '/');
};
const isOpen = (item) => item.children?.some((child) => segmentMatch(child.route)) || false;

// 🔹 Aktif
const isActive = (route) => segmentMatch(route);

function toggleSidebar() {
  const html = document.documentElement;
  if (typeof window !== 'undefined' && window.Helpers && typeof window.Helpers.toggleCollapsed !== 'undefined') {
    try { window.Helpers.toggleCollapsed(); return } catch (e) {}
  }
  html.classList.toggle('layout-menu-expanded');
}
</script>

<style scoped>
.menu-header-text {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.menu-item.active > .menu-link {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
  font-weight: 600;
}

.menu-icon {
  width: 18px;
  text-align: center;
}

.menu-sub .menu-item.active > .menu-link {
  background-color: rgba(13, 110, 253, 0.05);
  color: #0d6efd;
}
.layout-menu { height: 100vh; }
.layout-menu .menu-inner { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,.15) transparent; }
.layout-menu .menu-inner::-webkit-scrollbar { width: 6px; }
.layout-menu .menu-inner::-webkit-scrollbar-track { background: transparent; }
.layout-menu .menu-inner::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,.15); border-radius: 8px; }
</style>
