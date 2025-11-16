import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AdminLayout from './Layouts/AdminLayout.vue';
import GuestLayout from './Layouts/GuestLayout.vue';
import AppLayout from './Layouts/AppLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Halaman publik (tanpa login)
const GuestPages = [
  'Welcome',
  'PrivacyPolicy',
  'Blog',
  'BlogDetail',
  'Help',
];

// Halaman auth
const AppPages = [
];

// Halaman embed (tanpa layout)
const noLayoutPages = [
    'Profile/Show',
    'Auth/Login',
    'Auth/Register',
    'Auth/ForgotPassword',
    'Auth/ResetPassword',
    'Auth/VerifyEmail',
    'Embed/Preview',
];

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: async (name) => {
    const page = await resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    );

    if (noLayoutPages.includes(name)) {
      // Tanpa layout sama sekali
      page.default.layout = null;
    } else if (GuestPages.includes(name)) {
      // Halaman publik (guest)
      page.default.layout ??= GuestLayout;
    } else if (AppPages.includes(name)) {
      // Halaman login/register
      page.default.layout ??= AppLayout;
    } else {
      // Default: halaman admin
      page.default.layout ??= AdminLayout;
    }

    return page;
  },
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
