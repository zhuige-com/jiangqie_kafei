<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

class JiangQie_API_Other_Controller extends JiangQie_API_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'other';
	}

	public function register_routes()
	{
		//上传图片
		register_rest_route($this->namespace, '/' . $this->module . '/upload', [
			[
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => [$this, 'upload'],
				'permission_callback' => '__return_true',
			]
		]);
	}

	/**
	 * 上传图片
	 */
	public function upload($request)
	{
		$image = $_FILES['image'];

		$upload_dir = wp_upload_dir(null, false);
		$res = $this->uploadOne($image, $upload_dir['path']);
		if (is_wp_error($res)) {
			return $this->make_error('上传失败');
		}

		if (isset($res['error'])) {
			return $this->make_error($res['error']);
		}

		$url = $upload_dir['url'] . '/' . $res['file'];

		return $this->make_success([
			'src' => $url,
		]);
	}

	/**
	 * @desc 单文件上传
	 * @param string $file,上传文件信息数组
	 * @param string $path,上传路径
	 * @param int $max = 2M,最大上传大小
	 * @return bool|string,成功返回文件名，失败返回false
	 */
	private function uploadOne($file, $path, $max = 20000000)
	{
		//判定文件有效性
		if (!isset($file['error']) || count($file) != 5) {
			return ['error' => '错误的上传文件！'];
		}
		//路径判定
		if (!is_dir($path)) {
			return ['error' => '存储路径不存在！'];
		}
		//判定文件是否正确上传
		switch ($file['error']) {
			case 1:
			case 2:
				return ['error' => '文件超过服务器允许大小！'];
			case 3:
				return ['error' => '文件只有部分被上传！'];
			case 4:
				return ['error' => '没有选中要上传的文件！'];
			case 6:
			case 7:
				return ['error' => '服务器错误！'];
		}

		//判定文件类型
		if (!in_array($file['type'], array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'))) {
			return ['error' => '当前上传的文件类型不允许！'];
		}

		//判定业务大小
		if ($file['size'] > $max) {
			return ['error' => '当前上传的文件超过允许的大小！当前允许的大小是：' . (string) ($max / 1000000) . 'M'];
		}

		//获取随机名字
		$filename = $this->getRandomName($file['name']);

		//移动上传的临时文件到指定目录
		$filepath = $path . '/' . $filename;
		if (move_uploaded_file($file['tmp_name'], $filepath)) {

			// 导入媒体库
			$res = jiangqie_free_import_image2attachment($filepath);
			if (!is_wp_error($res)) {
				$filename = $res;
			}

			//成功
			return ['file' => $filename];
		} else {
			//失败
			return ['error' => '文件移动失败！'];
		}
	}

	/**
	 * @desc 获取随机文件名
	 * @param string $filename,文件原名
	 * @param string $prefix,前缀
	 * @return string,返回新文件名
	 */
	private function getRandomName($filename, $prefix = 'jq')
	{
		//取出源文件后缀
		$ext = strrchr($filename, '.');
		//构建新名字
		$new_name = $prefix . time();
		//增加随机字符（6位大写字母）
		for ($i = 0; $i < 6; $i++) {
			$new_name .= chr(mt_rand(65, 90));
		}
		//返回最终结果
		return $new_name . $ext;
	}
}
