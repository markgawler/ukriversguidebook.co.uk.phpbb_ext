module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
	RemoteHost: process.env.ukrgbRemoteHost,
	RemoteUser: process.env.ukrgbRemoteUser,
	privateKeyFile: process.env.ukrgbPrivateKeyFile,
       
    synchard: {
    	localdests: {
    		options: {
              	args: ['-av','--delete'],
              },
              files: {
                  'ext/ukrgb/core/': ['vendor']
              }
    	},
        remotedest: {
            options: {
            	args: ['-av','--delete'],
                ssh: true,
                privateKey: "<%=privateKeyFile%>"
            },
            files: {
                '<%=RemoteUser%>@<%=RemoteHost%>:/var/www/ukrgb/phpbb/ext/': ['ext/ukrgb']
            }
        }
    },

    watch: {
      styles: {
        files: ['ext/**/*'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    },
    
    compress: {
		main: {
			options: {
				mode: 'tgz',
				archive: 'ukrgb_ext.tar.gz'
			    },
			    files: [{src: ['ext/**'], dest: '.'}]
		 }
	},    
  });
  grunt.registerTask('sync', ['synchard']);
  grunt.registerTask('dist', ['compress']);
  grunt.registerTask('default', ['watch']);
};