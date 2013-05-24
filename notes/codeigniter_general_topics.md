# CodeIgniter General Topics

## Design and Architectural Goals
1. 动态加载（按需加载）
2. 松耦合（组件之间依赖性低）
3. 组件专一（每个组件有一个非常小的专注目标，每个类高度自治）

## URLs
1. Search-engine and human friendly: segment-based（pathinfo）
2. Removing the index.php file

	RewriteEngine on
	RewriteCond $1 !^(index\.php|images|robots\.txt)
	RewriteRule ^(.*)$ /index.php/$1 [L]

3. Adding a URL Suffix（You can specify a suffix）
4. Enabling Query Strings

## Controllers 
1. Controller = Module = Class
2. Method = Action
3. Passing URI Segments to your Actions directly

	/index.php/module/action/2013/05
	
	// the method will be passed URI segments 3 and 4("2013","05")
	class Blog extends CI_Controller{
		public function show($year,$month){
			echo $year;
			echo $month;
		}
	}
	
4. Defining a Default Controller
5. Processing Output
6. Private Methods(Hidden from public access)
7. Organizing Your Controllers into Sub-folders(Like group in ThinkPHP)

## Views
A view is simply a web page, or a page fragment, like a header, footer, sidebar, etc.

1. Loading a View. To load a particular view file you will use the following function:
	
	$this->load->view('name');
	
2. Loading multiple views
	
	$this->load->view('header');
	$this->load->view('content');
	$this->load->view('footer');
	
3. Storing Views within Sub-folders
4. Returning Views as data.

## Models
1. Loading a Model

	$this->load->model('model_name');
	
2. Storing Models within Sub-folders.

	$this->load->model('blog/queries');
	
3. Auto-loading Models
	
## Helper Functions 辅助函数/助手函数/快捷函数
1. Loading a Helper
	
	$this->load->helper('name');
	
2. Loading Multiple Helpers

	$this->load->helper(array('helper1','helper2'));
	
3. Auto-loading Helpers

## Using CodeIgniter Libraries 使用CI类库
All of the available libraries are located in your system/libraries folder.

## Creating Libraries
Your library classes should be placed within your application/libraries folder.

## Using CI Drivers

## Creating Drivers

## Creating Core Classes

## Hooks-Extending the Framework Core

## Auto-loading Resources
1. Core Classes
2. Helper Classes
3. Config files.
4. Language files.
5. Model files.

## Common Functions

## URI Routing
1. Wildcards(通配符)
2. Regular Expressions

## Error Handling
1. show_error
2. show_404
3. log_message
	1. Error Messages
	2. Debug Messages
	3. Infomational Messages.


## Web Page Caching

## Profiling Your Application
The profiler is like Trace/Monitor

## Running via the CLI(Command Line Interface)

## Managing your Applications
1. Renaming the Application Folder
2. Relocating your Application Folder
3. Running Multiple Applications with one CI Installation

## Handling  Multiple Environments
1. development 开发环境
2. production 生产环境

## Alternate PHP Syntax for View Files
1. Automatic short tag support
2. Alternative Echos.
3. Alternative Control Structures.

## Security
1. URI Security
	1. Alpha-numeric text
	2. Tilde: ~
	3. Period: .
	4. Colon: :
	5. Underscore: _
	6. Dash -
2. Register_globals
During system initialization all global variables are unset, except $_GET,$_POST,$_COOKIE
3. error_reporting
4. magic-quotes_runtime
The magic_quotes_runtime directive is turned off during system initialization.

### Best Practices
1. Filter the data as if it were tainted.
2. Validate the data to ensure it conforms to the correct type,length,size,etc.
3. Escape the data before submitting it into your database.

CI provides the following functions to assist in this process:
1. XSS Filtering(Cross Site Scripting Filter)
2. Validate the data(Form Validation Class)
3. Escape all data before database insertion.

## General Style and Syntax
1. Files should be saved with Unicode(UTF-8) encoding.
2. All PHP files should omit the closing PHP tag, and instead use a comment block to mark the end of file.
3. Class and Method Naming
4. Variable Names
5. Commenting
6. Constant
7. TRUE,FALSE, and NULL keyword should always be fully uppercase.
8. Logical Operators(逻辑操作符)
9. Comparing return Values and Typecasting
10. Debugging code
11. Whitespace in Files.No whitespace can precede the opening PHP tag or follow the closing PHP tag.
12. Compatibility.
13. Private Methods and Variables should be prefixed with an underscore.








