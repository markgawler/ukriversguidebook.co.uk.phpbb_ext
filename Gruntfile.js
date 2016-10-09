module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
	RemoteHost: process.env.ukrgbRemoteHost,
	RemoteUser: process.env.ukrgbRemoteUser,
	privateKeyFile: process.env.ukrgbPrivateKeyFile,
       
    synchard: {
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
    }
  });
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};