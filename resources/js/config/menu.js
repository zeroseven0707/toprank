export default [
  // ========================================================
  // 📊 DASHBOARD
  // ========================================================
  { header: "DASHBOARD" },
  {
    title: "Dashboard",
    icon: "ti ti-layout-dashboard",
    route: "/dashboard",
    permission: "view dashboard",
  },

  // ========================================================
  // 🔐 ACCESS CONTROL
  // ========================================================
  { header: "ACCESS CONTROL" },
  {
    title: "Users",
    icon: "ti ti-users",
    route: "/access/users",
    permission: "view users",
  },
  {
    title: "Roles & Permissions",
    icon: "ti ti-key",
    route: "/access/roles",
    permission: "view roles",
  },

  // ========================================================
  // 🗺️ MASTER DATA
  // ========================================================
  { header: "MASTER DATA" },
  {
    title: "City / Place Master",
    icon: "ti ti-map-pin",
    route: "/settings/cities",
    permission: "view cities",
  },
  {
    title: "Content Categories",
    icon: "ti ti-category",
    route: "/settings/content-categories",
    permission: "view content-categories",
  },

  // ========================================================
  // 🧩 CONTENT MANAGEMENT
  // ========================================================
  { header: "CONTENT MANAGEMENT" },
  {
    title: "Section Settings",
    icon: "ti ti-layout-collage",
    route: "/settings/sections",
    permission: "view sections",
  },
  {
    title: "Url Crawl Sources",
    icon: "ti ti-link",
    route: "/settings/url-crawls",
    permission: "view url-crawls",
  },
  {
    title: "Data Crawling",
    icon: "ti ti-database-import",
    route: "/settings/data-crawling",
    permission: "view data-crawling",
  },
  {
    title: "Content",
    icon: "ti ti-file-description",
    route: "/settings/content",
    permission: "view content",
  },

  // ========================================================
  // 📰 BLOG MANAGEMENT
  // ========================================================
  { header: "BLOG MANAGEMENT" },
  {
    title: "Blog Categories",
    icon: "ti ti-folder",
    route: "/settings/blog-categories",
    permission: "view blog-categories",
  },
  {
    title: "Blog Posts",
    icon: "ti ti-article",
    route: "/admin/blogs",
    permission: "view blogs",
  },

  // ========================================================
  // 🔒 LEGAL & POLICY
  // ========================================================
  { header: "LEGAL & POLICY" },
  {
    title: "Privacy Policy",
    icon: "ti ti-shield-check",
    route: "/settings/privacy-policy",
    permission: "view privacy-policy",
  },
]
