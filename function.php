<?php

if(strpos($_SERVER[REQUEST_URI], "init") == 1){
echo "";
}elseif(strpos($_SERVER[REQUEST_URI], "createConfig.php") == 1){
echo "";
}else{


if (file_exists("config/config.php")) {
    include_once 'config/config.php';

} else {

$actual_link = "$_SERVER[HTTP_HOST]";
    header("Location: http://".$actual_link."/init");
    exit();
    echo "Die Datei $filename existiert nicht";
}

}
function checkSiteDestination($url)
{
	$setSiteDestination = "";
	$SiteDestination = "";
	if(strpos($url, "kb_edit/") == 1){
		$setSiteDestination = "KB - Editor";
	}elseif(strpos($url, "kb/") == 1){
		$setSiteDestination = "KB";
	}elseif(strpos($url, "kb_search/") == 1){
		$setSiteDestination = "KB - Search";
	}elseif(strpos($url, "kb_new/") == 1){
		$setSiteDestination = "KB - New";
	}elseif(strpos($url, "init") == 1){
                $setSiteDestination = "init";
        }else{
		$setSiteDestination = "Webseite";
	}
	return $TL_SiteName . $setSiteDestination;
}

function sqlQuery($sql_Query)
{
	global $KB_DATABASE;
	if(!$sqlResult = $KB_DATABASE->query($sql_Query)){
		die($sqlQuery . '3 There was an error running the query [' . $KB_DATABASE->error . ']');
	}
	echo $KB_ID;
    return $sqlResult;   
}

function checkDatabase($KB_DATABASE){
	if($KB_DATABASE->connect_errno > 0){
		return false;
	}
	return true;

if ($KB_DATABASE->connect_errno) {
    printf("Connect failed: %s\n", $KB_DATABASE	->connect_error);
	echo "asdfasdfasdf";
	return "1";
}
return "2";
}

function randomString($size) {
   $chars = '0123456789';
   $chars .= 'abcdefghijklmnopqrstuvwxyz';
   $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $chars .= '()!.:=';
 
   //String wird generiert
   $str = '';
   $chars_size = strlen($chars);
   for ($i=0; $i<$size; $i++) {
      $str .= $chars[rand(0,$chars_size-1)];
   }
   return $str;
}

$LOAD_JS_SLIDER = "
        $(function() {
          $('#slides').slidesjs({	
            height: 235,
            navigation: false,
            pagination: false,
            effect: {
              fade: {
                speed: 400
              }
            },
            callback: {
                start: function(number)
                {			
                    $('#slider_content1,#slider_content2,#slider_content3').fadeOut(500);
                },
                complete: function(number)
                {			
                    $('#slider_content' + number).delay(500).fadeIn(1000);
                }		
            },
            play: {
                active: false,
                auto: true,
                interval: 6000,
                pauseOnHover: false,
                effect: 'fade'
            }
          });
        });
";

$LOAD_DIRTYSTATE = "
$(window).on('beforeunload', function(){
	if(tinyMCE.get('KB_Textarea').isDirty()){
		return 'This creates a pop-up';
	}
});
";

$INIT_TINYMCE = "


 function myFileBrowser (field_name, url, type, win) {

    //alert('Field_Name: ' + field_name + 'nURL: ' + url + 'nType: ' + type + 'nWin: ' + win); // debug/testing

    /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: '?PHPSESSID=88p0n70s9dsknra96qhuk6etm5').
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

    var cmsURL = '/filemanager/dialog.php?type=image&field_id=image_link';    // script URL - use an absolute path!
    if (cmsURL.indexOf('?') < 0) {
        //add the type as the only query parameter
        //cmsURL = cmsURL + '?type=' + type;
    }
    else {
        //add the type as an additional query parameter
        // (PHP session ID is now included if there is one at all)
        cmsURL = cmsURL + '&type=' + type;
    }

    tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'My File Browser',
        width : 420,  // Your dimensions may differ - toy around with them!
        height : 400,
        resizable : 'yes',
        inline : 'yes',  // This parameter only has an effect if you use the inlinepopups plugin!
		onsubmit: function(e) {
			
		alert('asdf'+top.tinymce.activeEditor.getWindows().toString());	
			
			},
		            buttons: [{
                  text: 'Submit',
                  onclick: 'submit'
               }, {
                  text: 'Cancel',
                  onclick: 'close'
               }],
        close_previous : 'no'
    }, {
        window : win,
        input : field_name
    });
    return false;
  }



function myImagePicker(callback, value, meta) {
    tinymce.activeEditor.windowManager.open({
        title: 'Image Browser',
        url: '/modules/tinymce/plugins/browser/upload.html?type=' + meta.filetype,
        width: 800,
        height: 550,
    }, {
        oninsert: function (url) {
            callback(url);
        }
    });
}

tinymce.PluginManager.add('test_widgets',function(editor,url){
	editor.addButton('test_widgets',{
		title: 'Widgets',
		text: 'Widgets',
		name: 'Widgets',
		id: 'Widgets',
		onclick: function(){
			editor.windowManager.open({
				title: 'Test Widgets',
				body: [
          {
            type   : 'combobox',
            name   : 'Source',
            label  : 'Source',
            items : ['Test', 'Test 2'],
			oninsert: function(e) {alert('Click')}
          },
          {
            type   : 'button',
            name   : 'button',
            label  : 'button',
            text   : 'My Button',
            onclick: function(e) {
	
myFileBrowser('Widgets','','image','d');
	
   // tinymce.activeEditor.windowManager.open({
        // title: 'Image Browser',
        // url: '/modules/tinymce/plugins/browser/upload.html?type=' + meta.filetype,
        // width: 800,
        // height: 550,
    // }, {
        // oninsert: function (url) {
            // callback(url);
        // }
    // });

	
// tinyMCE.activeEditor.windowManager.open({
   // url: '/filemanager/dialog.php?type=1&field_id=image_link',
   // width : 720,
   // height : 540
// },  {
         // oninsert: function (url) {
			 // alert('asdf');
            // callback(url);
         // },
		 
     // });
				
				
				}
          },
        ]
			});
		},
	});
});

tinymce.init(
{
  selector: 'textarea',
  automatic_uploads: true,
  height: 500,
  theme: 'modern',
  file_browser_callback : 'myFileBrowser',
  extended_valid_elements : 'img[class|src|border=0|alt|rel|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code codesample fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools image filemanager test_widgets'
  ],
  
   external_filemanager_path:'/filemanager/',
   filemanager_title:'Responsive Filemanager' ,
   external_plugins: { 'filemanager' : '/filemanager/plugin.min.js'},
  
  
     setup : function(ed) {
      ed.onClick.add(function(ed, e) {
		  alert('asdf');
          console.debug('Editor was clicked: ' + e.target.nodeName);
      });
   },
  
  
  setup: function (editor) {

	  
    editor.addButton('Insert_kb', {
      text: 'Link-Artikel',
      icon: false,


           onclick : function() {

// Opens a HTML page inside a TinyMCE dialog and pass in two parameters
editor.windowManager.open({
    title: 'Search Dialog',
    url: '/dialog_search.html',
    width: 700,
    height: 600
});

}
    });
	
	
	    editor.addButton('Insert_kb2', {
      text: 'Link-Artikel2',
      icon: false,


           onclick : function() {

// Opens a HTML page inside a TinyMCE dialog and pass in two parameters
editor.windowManager.open({
    title: 'Insert/edit image',
    url: '/dialog_upload_image.html',
    width: 500,
    height: 250
});

}
    });
	
	
  }
  ,
  style_formats: [
    { title: 'Headers', items: [
      { title: 'h2', block: 'h2' },
      { title: 'h3', block: 'h3' },
      { title: 'h4', block: 'h4' },
      { title: 'h5', block: 'h5' },
      { title: 'h6', block: 'h6' }
    ] },

    { title: 'Blocks', items: [
      { title: 'p', block: 'p' },
      { title: 'div', block: 'div' },
      { title: 'pre', block: 'pre' }
    ] },

    { title: 'Containers', items: [
      { title: 'section', block: 'section', wrapper: true, merge_siblings: false },
      { title: 'article', block: 'article', wrapper: true, merge_siblings: false },
      { title: 'blockquote', block: 'blockquote', wrapper: true },
      { title: 'hgroup', block: 'hgroup', wrapper: true },
      { title: 'aside', block: 'aside', wrapper: true },
      { title: 'figure', block: 'figure', wrapper: true }
    ] }
  ],
  visualblocks_default_state: true,
  end_container_on_empty_block: true,

  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons codesample Insert_kb Insert_kb2 test_widgets',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  

  content_css: [
//    '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
//    '//www.tinymce.com/css/codepen.min.css'
'/style/css/style.css',
  ]
  
  
  
 }
)
 "

?>
