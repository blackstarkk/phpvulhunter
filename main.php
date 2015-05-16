<?php

require_once 'global.php';

header("Content-type:text/html;charset=utf-8") ;

/**
 * 处理单个文件的静态检测
 * 输入PHP文件
 * @param string $path
 */
function load_file($path){
	$cfg = new CFGGenerator() ;
	$cfg->getFileSummary()->setPath($path);
	
	$visitor = new MyVisitor() ;
	$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative) ;
	$traverser = new PhpParser\NodeTraverser ;

	$code = file_get_contents($path);
	$stmts = $parser->parse($code) ;
	$traverser->addVisitor($visitor) ;
	$traverser->traverse($stmts) ;
	$nodes = $visitor->getNodes() ;
	
	$pEntryBlock = new BasicBlock() ;
	$pEntryBlock->is_entry = true ;
	
	//开始分析
	$cfg->CFGBuilder($nodes, NULL, NULL, NULL) ;
}

//1、从web ui中获取并加载项目工程
// $project_path = $_POST['path'] ;
// $scan_type = $_POST['type'] ;
$project_path = "D:/MySoftware/wamp-php-5.3/www/ecshop/upload/respond.php" ;


//2、初始化模块
// $project_path = 'F:/wamp/www/phpvulhunter/test';
$initModule = new InitModule() ;
$initModule->init($project_path) ;


//3、循环每个文件  进行分析工作
if(is_file($project_path)){
	load_file($project_path) ;
}elseif (is_dir($project_path)){
	$dirs = FileUtils::getDir($project_path) ;
	foreach ($dirs as $dir){
		$path_list = FileUtils::getPHPfile($dir) ;
		foreach ($path_list as $path){
			load_file($path) ;
		}
	}
}else{
	//请求不合法
	echo "<script>alert('工程不存在！');</script>" ;
	exit() ;
}


//4、获取ResultContext  传给template
$results = ResultContext::getInstance() ;




?>