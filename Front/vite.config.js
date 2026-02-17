import { sync } from "glob";

// export default {
//   build: {
//     outDir: "../dist",
//     emptyOutDir: true,
//     rollupOptions: {
//       input: sync("./src/**/*.html".replace(/\\/g, "/")),
//     },
//     // output: {
//     //   entryFileNames: "assets/js/[name].[hash].js", // فایل‌های JS در assets/js
//     //   chunkFileNames: "assets/js/[name].[hash].js", // فایل‌های Chunk در assets/js
//     //   assetFileNames: ({ name }) => {
//     //     if (name && name.endsWith(".html")) {
//     //       return "src/views/[name].[hash][extname]"; // فایل‌های HTML در src/views
//     //     } else if (name && name.endsWith(".css")) {
//     //       return "assets/css/[name].[hash][extname]"; // فایل‌های CSS در assets/css
//     //     } else if (name && /\.(png|jpe?g|svg|gif)$/.test(name)) {
//     //       return "assets/images/[name].[hash][extname]"; // تصاویر در assets/images
//     //     }
//     //     return "assets/[name].[hash][extname]"; // سایر دارایی‌ها
//     //   },
//     // },
//   },
// };
export default {
  server: {
    host: true,
    port: 3001,          
    strictPort: true,
    proxy: {
      "/api": {
        target: "http://192.168.0.159:3000",
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: "../dist",
    emptyOutDir: true,
    rollupOptions: { input: sync("./src/**/*.html".replace(/\\/g, "/")) },
  },
};

