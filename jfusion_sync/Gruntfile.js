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
            	exclude: ['css'],
                ssh: true,
                privateKey: "<%=privateKeyFile%>"

            },
            files: {
            	'<%=RemoteUser%>@<%=RemoteHost%>:/var/www/ukrgb/phpbb/ext/jfusion': ['components/com_jfusion/plugins/phpbb31/jfusion/'],
//            	'<%=RemoteUser%>@<%=RemoteHost%>:/var/www/ukrgb/joomla/components/com_jfusion/': ['components/com_jfusion/'],
//            	'<%=RemoteUser%>@<%=RemoteHost%>:/var/www/ukrgb/joomla/administrator/components/com_jfusion/': ['administrator/components/com_jfusion/'],                
            }
        }
    },

    watch: {
      site: {
        files: ['components/**/*','administrator/**/*'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
 
  grunt.file.setBase('../../org.jfusion.jfusion/')
  
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};