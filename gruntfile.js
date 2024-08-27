module.exports = function (grunt) {
    grunt.initConfig({
        copy: {
            assets: {
                files: [
                    {
                        expand: true,
                        cwd: "src/images/",
                        src: "**",
                        dest: "assets/images/",
                    },
                    {
                        expand: true,
                        cwd: "src/fonts/",
                        src: "**",
                        dest: "assets/fonts/",
                    },
                    {
                        expand: true,
                        cwd: "src/admin/images/",
                        src: "**",
                        dest: "admin/assets/images/",
                    },
                    {
                        expand: true,
                        cwd: "src/admin/css/",
                        src: "**",
                        dest: "admin/assets/css/",
                    },
                    {
                        expand: true,
                        cwd: "node_modules/goodshare.js/",
                        src: "goodshare.min.js",
                        dest: "assets/vendor/js",
                        ext: ".min.js",
                    },
                ],
            },
        },

        sass: {
            options: {
                implementation: require("sass"),
                sourceMap: false,
            },
            outputStyle: "expanded",
            dist: {
                files: [
                    {
                        // widgets
                        expand: true,
                        cwd: "src/scss/widgets",
                        src: "**",
                        dest: "assets/css/",
                        ext: ".css",

                        rename: function (dest, src) {
                            return dest + src.replace(/(.+)\.css$/, "upk-$1.css");
                        },
                    },
                    {
                        expand: true,
                        cwd: "src/scss/",
                        src: [
                            "*",
                            "!admin.scss",
                            "!elementor.scss",
                            "!overrides.scss",
                            "!**/fonts/**",
                            "!**/widgets/**",
                        ],
                        dest: "assets/css/",
                        ext: ".css",

                        rename: function (dest, src) {
                            return dest + src.replace(/(.+)\.css$/, "upk-$1.css");
                        },
                    },
                    {
                        expand: true,
                        cwd: "src/scss/",
                        src: ["admin.scss"],

                        dest: "admin/assets/css/",
                        ext: ".css",

                        rename: function (dest, src) {
                            return dest + src.replace(/(.+)\.css$/, "upk-$1.css");
                        },
                    },
                    {
                        expand: true,
                        cwd: "src/scss/",
                        src: ["admin-notice.scss"],

                        dest: "admin/assets/css/",
                        ext: ".css",

                        rename: function (dest, src) {
                            return dest + src.replace(/(.+)\.css$/, "upk-$1.css");
                        },
                    },
                ],
            },
        },

        rtlcss: {
            siteRTL: {
                options: {
                    opts: {
                        clean: true,
                    },
                    plugins: [],
                    saveUnmodified: true,
                },

                expand: true,
                cwd: "assets/css/",
                src: ["**/*.css", "!**/*.rtl.css"],
                dest: "assets/css/",
                ext: ".rtl.css",
            },
            adminRTL: {
                options: {
                    opts: {
                        clean: true,
                    },
                    plugins: [],
                    saveUnmodified: true,
                },
                expand: true,
                cwd: "admin/assets/css/",
                src: ["**/*.css", "!**/*.rtl.css"],
                dest: "admin/assets/css/",
                ext: ".rtl.css",
            },
        },

        terser: {
            dist: {
                options: {
                    mangle: true,
                },
                files: [
                    {
                        expand: true,
                        cwd: "src/admin/js/",
                        src: "*.js",
                        dest: "admin/assets/js/",
                        ext: ".min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/js/widgets/",
                        src: "*.js",
                        dest: "assets/js/widgets/",
                        ext: ".min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/js/extensions/",
                        src: "*.js",
                        dest: "assets/js/extensions/",
                        ext: ".min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/vendor/js/",
                        src: "jquery.scrolline.js",
                        dest: "assets/vendor/js/",
                        ext: ".scrolline.min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/vendor/js/",
                        src: ["newsticker.js", "fslightbox.js", "!jquery.scrolline.js"],
                        dest: "assets/vendor/js/",
                        ext: ".min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/js/",
                        src: ["upk-site.js", "upk-editor.js"],
                        dest: "assets/js/",
                        ext: ".min.js",
                    },
                    {
                        expand: true,
                        cwd: "src/controls/assets/js/",
                        src: ["upk-dynamic-select.js"],
                        dest: "includes/controls/assets/js/",
                        ext: ".min.js",
                    },
                ],
            },
        },

        concat: {
            widgets: {
                src: [
                    "assets/js/widgets/*",
                    "assets/js/extensions/*",
                    "assets/vendor/js/*",
                ],
                dest: "assets/js/upk-all-scripts.min.js",
                ext: "min.js",

                options: {
                    banner: ";(function($, elementor){\n'use strict';\n",
                    footer: "\n})(jQuery, window.elementorFrontend);",
                },
            },
        },

        watch: {
            styles: {
                files: ["src/scss/*.scss", "src/scss/**/*.scss"], // which files to watch
                tasks: ["sass", "rtlcss"],
                options: {
                    nospawn: true,
                },
            },
            scripts: {
                files: ["src/js/*.js", "src/js/**/*.js", "package.json"],
                tasks: ["terser", "concat"],
                options: {
                    spawn: false,
                },
            },
        },
    });

    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-rtlcss");
    grunt.loadNpmTasks("grunt-terser");
    grunt.loadNpmTasks("grunt-contrib-concat");
    grunt.loadNpmTasks("grunt-contrib-watch");

    if (process.env.NODE_ENV === "development") {
        grunt.registerTask("default", [
            "copy",
            "sass",
            "rtlcss",
            "terser",
            "concat",
            "watch",
        ]);
    } else {
        grunt.registerTask("default", [
            "copy",
            "sass",
            "rtlcss",
            "terser",
            "concat",
        ]);
    }
};
