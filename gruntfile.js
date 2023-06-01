module.exports = function(grunt) {
    const jit_grunt = require('jit-grunt');
    const sass = require('node-sass');

    grunt.initConfig({
        sass: {
           dist: {
                options: {
                    implementation: sass,
                },
                outputStyle: 'expanded',
                files: {
                    'assets/css/upk-site.css'   : 'assets/scss/site.scss',
                    'assets/css/upk-font.css'   : 'assets/scss/font.scss',
                    'assets/css/upk-editor.css' : 'assets/scss/editor.scss',
                    'assets/css/upk-preview.css': 'assets/scss/preview.scss',
                    //admin css
                    'admin/assets/css/upk-admin.css'          : 'assets/scss/admin.scss',

                    //Widget Css
                    'assets/css/upk-alex-grid.css'            : 'assets/scss/widgets/alex-grid.scss',
                    'assets/css/upk-alter-grid.css'           : 'assets/scss/widgets/alter-grid.scss',
                    'assets/css/upk-banner.css'               : 'assets/scss/widgets/banner.scss',
                    'assets/css/upk-elite-grid.css'           : 'assets/scss/widgets/elite-grid.scss',
                    'assets/css/upk-hazel-grid.css'           : 'assets/scss/widgets/hazel-grid.scss',
                    'assets/css/upk-maple-grid.css'           : 'assets/scss/widgets/maple-grid.scss',
                    'assets/css/upk-ramble-grid.css'          : 'assets/scss/widgets/ramble-grid.scss',
                    'assets/css/upk-alex-carousel.css'        : 'assets/scss/widgets/alex-carousel.scss',
                    'assets/css/upk-category-carousel.css'    : 'assets/scss/widgets/category-carousel.scss',
                    'assets/css/upk-alter-carousel.css'       : 'assets/scss/widgets/alter-carousel.scss',
                    'assets/css/upk-elite-carousel.css'       : 'assets/scss/widgets/elite-carousel.scss',
                    'assets/css/upk-hazel-carousel.css'       : 'assets/scss/widgets/hazel-carousel.scss',
                    'assets/css/upk-maple-carousel.css'       : 'assets/scss/widgets/maple-carousel.scss',
                    'assets/css/upk-ramble-carousel.css'      : 'assets/scss/widgets/ramble-carousel.scss',
                    'assets/css/upk-alice-grid.css'           : 'assets/scss/widgets/alice-grid.scss',
                    'assets/css/upk-alice-carousel.css'       : 'assets/scss/widgets/alice-carousel.scss',
                    'assets/css/upk-harold-list.css'          : 'assets/scss/widgets/harold-list.scss',
                    'assets/css/upk-harold-carousel.css'      : 'assets/scss/widgets/harold-carousel.scss',
                    'assets/css/upk-paradox-slider.css'       : 'assets/scss/widgets/paradox-slider.scss',
                    'assets/css/upk-news-ticker.css'          : 'assets/scss/widgets/news-ticker.scss',
                    'assets/css/upk-post-accordion.css'       : 'assets/scss/widgets/post-accordion.scss',
                    'assets/css/upk-post-category.css'        : 'assets/scss/widgets/post-category.scss',
                    'assets/css/upk-tag-cloud.css'            : 'assets/scss/widgets/tag-cloud.scss',
                    'assets/css/upk-timeline.css'             : 'assets/scss/widgets/timeline.scss',
                    'assets/css/upk-featured-list.css'        : 'assets/scss/widgets/featured-list.scss',
                    'assets/css/upk-fanel-list.css'           : 'assets/scss/widgets/fanel-list.scss',
                    'assets/css/upk-author.css'               : 'assets/scss/widgets/author.scss',
                    'assets/css/upk-tiny-list.css'            : 'assets/scss/widgets/tiny-list.scss',
                    'assets/css/upk-social-share.css'         : 'assets/scss/widgets/social-share.scss',
                    'assets/css/upk-newsletter.css'           : 'assets/scss/widgets/newsletter.scss',
                    'assets/css/upk-buzz-list.css'            : 'assets/scss/widgets/buzz-list.scss',
                    'assets/css/upk-buzz-list-carousel.css'   : 'assets/scss/widgets/buzz-list-carousel.scss',
                    'assets/css/upk-scott-list.css'           : 'assets/scss/widgets/scott-list.scss',
                    'assets/css/upk-noxe-slider.css'          : 'assets/scss/widgets/noxe-slider.scss',
                    'assets/css/upk-recent-comments.css'      : 'assets/scss/widgets/recent-comments.scss',
                    'assets/css/upk-skide-slider.css'         : 'assets/scss/widgets/skide-slider.scss',
                    'assets/css/upk-amox-grid.css'            : 'assets/scss/widgets/amox-grid.scss',
                    'assets/css/upk-amox-carousel.css'        : 'assets/scss/widgets/amox-carousel.scss',
                    'assets/css/upk-static-social-count.css'  : 'assets/scss/widgets/static-social-count.scss',
                    'assets/css/upk-camux-slider.css'         : 'assets/scss/widgets/camux-slider.scss',
                    'assets/css/upk-crystal-slider.css'       : 'assets/scss/widgets/crystal-slider.scss',
                    'assets/css/upk-carbon-slider.css'        : 'assets/scss/widgets/carbon-slider.scss',
                    'assets/css/upk-pholox-slider.css'        : 'assets/scss/widgets/pholox-slider.scss',
                    'assets/css/upk-snog-slider.css'          : 'assets/scss/widgets/snog-slider.scss',
                    'assets/css/upk-exotic-list.css'          : 'assets/scss/widgets/exotic-list.scss',
                    'assets/css/upk-gratis-grid.css'          : 'assets/scss/widgets/gratis-grid.scss',


                    'assets/css/upk-all-styles.css'           : 'assets/scss/all-styles.scss',
                },
            },
        },
        rtlcss: {
            siteRTL: {
                // task options
                options: {
                    // rtlcss options
                    opts: {
                        clean: true
                    },
                    // rtlcss plugins
                    plugins: [],
                    // save unmodified files
                    saveUnmodified: true
                },
                expand: true,
                cwd: 'assets/css/',
                dest: 'assets/css/',
                src: ['**/*.css', '!**/*.rtl.css'],
                ext: '.rtl.css'
            },
            adminRTL: {
                // task options
                options: {
                    // rtlcss options
                    opts: {
                        clean: true
                    },
                    // rtlcss plugins
                    plugins: [],
                    // save unmodified files
                    saveUnmodified: true
                },
                expand: true,
                cwd: 'admin/assets/css/',
                dest: 'admin/assets/css/',
                src: ['**/*.css', '!**/*.rtl.css'],
                ext: '.rtl.css'
            }
        },

        concat: {
          js: {
              src: [

                    //common/helper widget js
                    // 'assets/js/common/helper.js',
              ],
              dest: 'assets/js/upk-site.js',
              options: {
                  banner: ';(function($, elementor){\n\'use strict\';\n',
                  footer: '\n})(jQuery, window.elementorFrontend);'
              },
          },
          allWidgetsJS: {
              src: [

                    //vendor scripts
                    'assets/vendor/js/goodshare.js',
                    'assets/vendor/js/newsticker.js',
                    // widget js
                    'assets/js/widgets/upk-alex-carousel.js',
                    'assets/js/widgets/upk-category-carousel.js',
                    'assets/js/widgets/upk-alter-carousel.js',
                    'assets/js/widgets/upk-elite-carousel.js',
                    'assets/js/widgets/upk-hazel-carousel.js',
                    'assets/js/widgets/upk-maple-carousel.js',
                    'assets/js/widgets/upk-ramble-carousel.js',
                    'assets/js/widgets/upk-alice-carousel.js',
                    'assets/js/widgets/upk-paradox-slider.js',
                    'assets/js/widgets/upk-news-ticker.js',
                    'assets/js/widgets/upk-harold-carousel.js',
                    'assets/js/widgets/upk-buzz-list-carousel.js',
                    'assets/js/widgets/upk-newsletter.js',
                    'assets/js/widgets/upk-noxe-slider.js',
                    'assets/js/widgets/upk-skide-slider.js',
                    'assets/js/widgets/upk-amox-carousel.js',
                    'assets/js/widgets/upk-static-social-count.js',
                    'assets/js/widgets/upk-camux-slider.js',
                    'assets/js/widgets/upk-crystal-slider.js',
                    'assets/js/widgets/upk-reading-progress.js',
                    'assets/js/widgets/upk-carbon-slider.js',
                    'assets/js/widgets/upk-pholox-slider.js',
                    'assets/js/widgets/upk-snog-slider.js',

                    // Extensiosns JS
                    'assets/js/extensions/upk-animations.js'
              ],
              dest: 'assets/js/upk-all-scripts.js',
              options: {
                  banner: ';(function($, elementor){\n\'use strict\';\n',
                  footer: '\n})(jQuery, window.elementorFrontend);'
              },
          }
        },

        terser: {
            my_target: {
                options: {
                    mangle: true
                },
                files: {

                    "admin/assets/js/upk-admin.min.js"                : ["admin/assets/js/upk-admin.js"],

                    // widget js
                    'assets/js/widgets/upk-alex-carousel.min.js'      : 'assets/js/widgets/upk-alex-carousel.js',
                    'assets/js/widgets/upk-category-carousel.min.js'  : 'assets/js/widgets/upk-category-carousel.js',
                    'assets/js/widgets/upk-alter-carousel.min.js'     : 'assets/js/widgets/upk-alter-carousel.js',
                    'assets/js/widgets/upk-elite-carousel.min.js'     : 'assets/js/widgets/upk-elite-carousel.js',
                    'assets/js/widgets/upk-hazel-carousel.min.js'     : 'assets/js/widgets/upk-hazel-carousel.js',
                    'assets/js/widgets/upk-maple-carousel.min.js'     : 'assets/js/widgets/upk-maple-carousel.js',
                    'assets/js/widgets/upk-ramble-carousel.min.js'    : 'assets/js/widgets/upk-ramble-carousel.js',
                    'assets/js/widgets/upk-alice-carousel.min.js'     : 'assets/js/widgets/upk-alice-carousel.js',
                    'assets/js/widgets/upk-paradox-slider.min.js'     : 'assets/js/widgets/upk-paradox-slider.js',
                    'assets/js/widgets/upk-news-ticker.min.js'        : 'assets/js/widgets/upk-news-ticker.js',
                    'assets/js/widgets/upk-harold-carousel.min.js'    : 'assets/js/widgets/upk-harold-carousel.js',
                    'assets/js/widgets/upk-buzz-list-carousel.min.js' : 'assets/js/widgets/upk-buzz-list-carousel.js',
                    'assets/js/widgets/upk-newsletter.min.js'         : 'assets/js/widgets/upk-newsletter.js',
                    'assets/js/widgets/upk-noxe-slider.min.js'        : 'assets/js/widgets/upk-noxe-slider.js',
                    'assets/js/widgets/upk-skide-slider.min.js'       : 'assets/js/widgets/upk-skide-slider.js',
                    'assets/js/widgets/upk-amox-carousel.min.js'      : 'assets/js/widgets/upk-amox-carousel.js',
                    'assets/js/widgets/upk-static-social-count.min.js': 'assets/js/widgets/upk-static-social-count.js',
                    'assets/js/widgets/upk-camux-slider.min.js'       : 'assets/js/widgets/upk-camux-slider.js',
                    'assets/js/widgets/upk-crystal-slider.min.js'     : 'assets/js/widgets/upk-crystal-slider.js',
                    'assets/js/widgets/upk-reading-progress.min.js'   : 'assets/js/widgets/upk-reading-progress.js',
                    'assets/js/widgets/upk-carbon-slider.min.js'      : 'assets/js/widgets/upk-carbon-slider.js',
                    'assets/js/widgets/upk-pholox-slider.min.js'      : 'assets/js/widgets/upk-pholox-slider.js',
                    'assets/js/widgets/upk-snog-slider.min.js'        : 'assets/js/widgets/upk-snog-slider.js',

                    // Extensiosns JS
                    'assets/js/extensions/upk-animations.min.js'           : 'assets/js/extensions/upk-animations.js',

                    // frontend js
                    'assets/js/upk-site.min.js'              : ['assets/js/upk-site.js'],
                    // editor js
                    'assets/js/upk-editor.min.js'            : ['assets/js/upk-editor.js'],
                    // dynamic query control script
                    'includes/controls/assets/js/upk-dynamic-select.min.js': ['includes/controls/assets/js/upk-dynamic-select.js'],

                    //Vendor JS
                    'assets/vendor/js/goodshare.min.js'                    : ['assets/vendor/js/goodshare.js'],
                    'assets/vendor/js/newsticker.min.js'                   : ['assets/vendor/js/newsticker.js'],
                    'assets/vendor/js/jquery.scrolline.min.js'             : ['assets/vendor/js/jquery.scrolline.js'],

                    // combine scripts min file
                    'assets/js/upk-all-scripts.min.js'                     : 'assets/js/upk-all-scripts.js',
                }
            }
        },

        watch: {
            styles: {
                files: ['assets/scss/*.scss', 'assets/scss/*/*.scss'], // which files to watch
                tasks: ['sass', 'rtlcss'],
                options: {
                    nospawn: true
                }
            },
            scripts: {
                files: ['assets/js/*/*.js', 'assets/vendor/js/*.js'],
                tasks: ['terser', 'concat'],
                options: {
                  spawn: false,
                },
            }
        }
    });

    grunt.loadNpmTasks('grunt-rtlcss');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-terser');
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-contrib-watch");

    grunt.registerTask('default', ['sass', 'rtlcss', 'concat', 'terser', 'watch']);
};