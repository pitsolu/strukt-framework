<?php

namespace Strukt\Console\Command;

use Strukt\Console\Input;
use Strukt\Console\Output;
use Strukt\Env;
use Strukt\Fs;
use Strukt\Type\Str;
use Strukt\Type\Json;
use Strukt\Type\Arr;
use Strukt\Templator;
use Strukt\Raise;
use Strukt\Ref;

use Strukt\Package\PkgDo;
use Strukt\Package\PkgRoles;
use Strukt\Package\PkgAudit;
use Strukt\Package\PkgBooks;
use Strukt\Package\PkgTests;

/**
* publish:package     Package Publisher
*
* Usage:
*
*       publish:package <pkg>
*
* Arguments:
*
*       pkg   package name
*/
class PackagePublisher extends \Strukt\Console\Command{

	public function __construct(){

		$this->packages = array(

			"pkg-do"=>PkgDo::class,
			"pkg-roles"=>PkgRoles::class,
			"pkg-audit"=>PkgAudit::class,
			"pkg-books"=>PkgBooks::class,
			"pkg-tests"=>PkgTests::class
		);
	}

	public function execute(Input $in, Output $out){

		$pkgname = $in->get("pkg");

		$vendor_pkg = Str::create(Env::get("root_dir"))
			->concat("/vendor/strukt/")
			->concat($pkgname)
			->yield();

		$app_ini = parse_ini_file(Str::create(Env::get("root_dir"))
				->concat("/")
				->concat(Env::get("rel_app_ini"))
				->yield());

		if(!array_key_exists("app-name", $app_ini))
			new Raise("cfg/app.ini[app-name] is undefined!");

		if(empty($app_ini["app-name"]))
			new Raise("cfg/app.ini[app-name] is not defined!");

		$pkgclass = $this->packages[$pkgname]; 

		if(!class_exists($pkgclass))
			new Raise(sprintf("Package %s is not installed!", $pkgclass));

		$pkg = Ref::create($pkgclass)->make()->getInstance();

		$appname = $app_ini["app-name"];

		Arr::create($pkg->getFiles())->each(function($key, $relpath) use ($vendor_pkg, $appname){

			$vendor_appbase = Str::create(Env::get("rel_appsrc_dir"))->concat("App")->yield();

			$qpath = Str::create(Env::get("root_dir"))
				->concat("/")
				->concat($relpath);

			if($qpath->contains($vendor_appbase))
				$qpath = $qpath->replace($vendor_appbase, sprintf("app/src/%s", $appname));

			if($qpath->endsWith(".sgf"))
				$qpath = $qpath->replace(".sgf", ".php");

			$actual_path = $qpath->yield();

			$vendor_file_path = Str::create($vendor_pkg)
				->concat("/package/")
				->concat($relpath)
				->yield();

			$path = pathinfo($actual_path);

			$qfilename = Str::create($path["filename"]);
			if($qfilename->startsWith("_")){

				$filename = $qfilename->replace("_", $appname)->yield();
				$actual_path = $qpath->replace($path["filename"], $filename);
			}

			Fs::mkdir($path["dirname"]);

			$file_content = Fs::cat($vendor_file_path);
			if(Str::create($vendor_file_path)->endsWith(".sgf") &&
				!Str::create($vendor_file_path)->contains("tpl/sgf"))
					$file_content = Templator::create($file_content, array(

						"app"=>$appname
					));

			if(Fs::isFile($actual_path))
				Fs::rename($actual_path, sprintf("%s~", $actual_path));

			Fs::touchWrite($actual_path, $file_content);
		});

		$out->add("Package successfully published\n");
	}
}	