<?php
require_once "config.php";

Class GitDeploy {
	/* commit message to deploy */
	public $commit_message = "Success GitDeploy Page";
	/* array of configuration */
	public $c = [];
	/* public temporary data */
	public $data = [];
	
	public function __construct() {
		$this->c['root'] = getcwd() . DIRECTORY_SEPARATOR . "deploy";
		$this->c['local']  = $this->c['root'] . DIRECTORY_SEPARATOR . REPONAME;
		$this->c['remote'] = "https://" . ACCESSTOKEN . "@github.com/" . USERNAME . "/" . REPONAME . ".git";
	}

	public function get($f){
		if(is_array($f)) {
			reset($f);
			return $this->{key($f)}[current($f)];
		} else {
			return $this->$f;
		}
	}

	public function git_exec($c) {
		echo "<pre>" . shell_exec("cd {$this->c['local']} && git $c 2>&1") . "\n</pre>";
	}

	public function send() {
		if( file_exists($this->c['local']) ) {
			$this->git_exec(" config user.name '" . USERNAME . "' ");
			$this->git_exec(" config user.email '" . USEREMAIL . "' ");
			$this->git_exec(" add . ");
			$this->git_exec(" commit -am '{$this->commit_message}' ");
			$this->git_exec(" push origin " . REPOBRANCH);
			echo "\nUpdate!";
		} else {
			$this->git_exec(" clone {$this->c['remote']} --branch " . REPOBRANCH);
			echo "\nCloned!";
		}
	}

	public function write_files() {
		$m = fopen($this->c['local'] . DIRECTORY_SEPARATOR . $this->data['filename'], "w") or die("Unable to open file!");
		fwrite($m, $this->data['html']);
		fclose($m);
	}
}

$x = new GitDeploy();
$x->commit_message = " This is a commit " . microtime(true);
$x->data["filename"] = "index123.html";
$x->data["html"] = "<html><head><title>Hello World!</title>\n</head>\n<body>Hello World</body>\n</html>\n<!--generated with GitDeploy by vinigomescunha -->";
$x->write_files();
$x->send();

