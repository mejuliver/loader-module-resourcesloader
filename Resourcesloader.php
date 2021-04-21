<?php
	namespace PDLoader;

	class Resourcesloader extends Loader{
		
		public function loadcss($dir=false,$url=false){

			$version = uniqid();

			if( !$url ){
				if( $this->config() && isset($this->config()['url'] ) ){
					$url = $this->config()['url'];
				}else{
					$url = $this->url();
				}
			}

			$css_dir = $this->config(true)['css_dir'];
			$async_dir = $this->config(true)['async_dir'].'/css/*';

			if( $dir && is_array($dir) ){
				if( isset( $dir['css'] ) ){
					$css_dir = $dir['css'].'/*';	
				}
				if( isset( $dir['async'] ) ){
					$async_dir = $dir['async'].'/*';
				}
			}elseif( $dir && !is_array($dir)){
				$css_dir = $dir['css'].'/*';
			}

			
			$pre_arr = [
				'css' => [],
				'async' => []
			];
				
			$async = [];

			$assets_raw = [];
			
			foreach (glob($css_dir) as $c) {
				$f = explode('.',$c);
				if( $f[count($f)-1] == 'css' ){
					$n = explode('--',$c);
					$assets_raw[] = ['src' => $url.$c,'or' => ( count( $n ) > 1 ) ? (int)$n[1] : 0 ];
				}
			}

			$assets = [];

			foreach( $assets_raw as $k => $a ){
				$m = [];
				foreach ($assets_raw as $a2) {
					$m[] = $a2['or'];
				}
				if( $a['or'] === 0 ){

					$a['or'] = max($m)+1;
				}
				$assets_raw[$k]['or'] = $a['or'];

				$assets[] = $a;
			}

			usort($assets,function($a,$b){
			  return $a['or']-$b['or'];
			});

			foreach (glob($async_dir) as $c) {
				$f = explode('.',$c);
				if( $f[count($f)-1] == 'css' ){
					$pre_arr['async'][] = $url.$c;
					$async[] = $url.$c;
				}

				$m = [];

				foreach ($assets as $k => $a) {
					$t = explode('/', $a['src']);
					$m[$k] = end($t);
				}

				$tt = explode('/',$url.$c);

				if( in_array(end($tt), $m ) ){
					array_splice($assets, array_search(end($tt), $m),1);
				}
			}

			foreach( $assets as $a ){
				$pre_arr['css'][] = $a;
				echo '<link href="'.$a['src'].'?v='.$version.'" type="text/css" rel="stylesheet">'."\n";
			}

			if( isset( $_GET['preview'] ) ){
				var_dump($pre_arr);
			}else{
				echo "<script async defer>var rcs_css = ".json_encode($async).";".trim(preg_replace('/\s+/', ' ',file_get_contents(__DIR__.'/loader-css.js')))."</script>\n";
			}
			
		}

		public function loadjs($dir=false,$url=false,$config=[]){

			$version = uniqid();

			if( !$url ){
				if( $this->config() && isset($this->config()['url'] ) ){
					$url = $this->config()['url'];
				}else{
					$url = $this->url();
				}
			}

			$js_dir = $this->config(true)['js_dir'];
			$async_dir = $this->config(true)['async_dir'].'/js/*';

			if( $dir && is_array($dir) ){
				if( isset( $dir['js'] ) ){
					$js_dir = $dir['js'].'/*';
				}
				if( isset( $dir['async'] ) ){
					$async_dir = $dir['async'].'/*';
				}
			}elseif( $dir && !is_array($dir)){
				$js_dir = $dir['js'].'/*';
			}


			$pre_arr = [
				'js' => [],
				'async' => []
			];

			$async = [];

			$assets_raw = [];

			foreach (glob($js_dir) as $c) {
				$f = explode('.',$c);
				if( $f[count($f)-1] == 'js' ){
					$n = explode('--',$c);
					$assets_raw[] = ['src' => $url.$c,'or' => ( count( $n ) > 1 ) ? (int)$n[1] : 0 ];
				}
			}

			$assets = [];

			foreach( $assets_raw as $k => $a ){
				$m = [];
				foreach ($assets_raw as $a2) {
					$m[] = $a2['or'];
				}
				if( $a['or'] === 0 ){

					$a['or'] = max($m)+1;
				}
				$assets_raw[$k]['or'] = $a['or'];

				$assets[] = $a;
			}

			usort($assets,function($a,$b){
			  return $a['or']-$b['or'];
			});


			foreach (glob($async_dir) as $c) {
				$f = explode('.',$c);
				if( $f[count($f)-1] == 'js' ){
					$pre_arr['async']['js'][] = $url.$c;
					$async[] =  $url.$c.'?v='.$version;
				}

				$m = [];

				foreach ($assets as $k => $a) {
					$t = explode('/', $a['src']);
					$m[$k] = end($t);
				}

				$tt = explode('/',$url.$c);

				if( in_array(end($tt), $m ) ){	
					array_splice($assets, array_search(end($tt), $m),1);
				}
			}

			foreach( $assets as $a ){
				$pre_arr['js'][] = $a;
				echo '<script src="'.$a['src'].'?v='.$version.'"></script>'."\n";
			}	
			
			foreach ($config as $c ) {
				$async[] = $c;
			}

			if( isset( $_GET['preview'] ) ){
				var_dump($pre_arr);
			}else{
				echo "<script async defer>var rcs_js= ".json_encode($async).";".trim(preg_replace('/\s+/', ' ',file_get_contents(__DIR__.'/loader-js.js')))."</script>\n";
			}
		}


		public function onBuild(){
			if( file_exists(__DIR__.'/test.php') ){
				unlink(__DIR__.'/test.php');
			}
			if( file_exists(__DIR__.'/package.json') ){
				unlink(__DIR__.'/package.json');
			}
			if( file_exists(__DIR__.'/package-lock.json') ){
				unlink(__DIR__.'/package-lock.json');
			}
			if( file_exists(__DIR__.'/dist/index.html') ){
				unlink(__DIR__.'/dist/index.html');
			}
			if( file_exists(__DIR__.'/index.html') ){
				unlink(__DIR__.'/index.html');
			}
			if( file_exists(__DIR__.'/.gitignore') ){
				unlink(__DIR__.'/.gitignore');
			}
			if( file_exists(__DIR__.'/src') ){
				$this->deleteFolder(__DIR__.'/src');
			}
			if( file_exists(__DIR__.'/.phpintel') ){
				$this->deleteFolder(__DIR__.'/.phpintel');
			}
			if( file_exists(__DIR__.'/.cache') ){
				$this->deleteFolder(__DIR__.'/.cache');
			}
			if( file_exists(__DIR__.'/.git') ){
				$this->deleteFolder(__DIR__.'/.git');
			}

		}

		private function deleteFolder($dir){
			if(file_exists($dir)){
				$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new \RecursiveIteratorIterator($it,
				             \RecursiveIteratorIterator::CHILD_FIRST);

				foreach($files as $file) {
					chmod($file->getRealPath(),0755);
				    if ($file->isDir()){
				        rmdir($file->getRealPath());
				    } else {
				        unlink($file->getRealPath());
				    }
				}
				rmdir($dir);
			}
			
		}	
		

	}