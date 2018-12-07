<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\model\Attachment as AttachmentModel;

use think\Image;
use think\File;
use think\Controller;

/**
 * 项目公共控制器
 * @package app\common\controller
 */
class Common extends Controller
{
    /**
     * 初始化
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function _initialize()
    {
        // 后台公共模板
        $this->assign('_admin_base_layout', config('admin_base_layout'));
        // 当前配色方案
        $this->assign('system_color', config('system_color'));
        // 输出弹出层参数
        $this->assign('_pop', $this->request->param('_pop'));
    }

    /**
     * 获取筛选条件
     * @author 蔡伟明 <314013107@qq.com>
     * @alter 小乌 <82950492@qq.com>
     * @return array
     */
    final protected function getMap()
    {
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');
        $select_field     = input('param._select_field/s', '');
        $select_value     = input('param._select_value/s', '');
        $search_area      = input('param._s', '');
        $search_area_op   = input('param._o', '');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[$search_field] = ['like', "%$keyword%"];
        }

        // 下拉筛选
        if ($select_field != '') {
            $select_field = array_filter(explode('|', $select_field), 'strlen');
            $select_value = array_filter(explode('|', $select_value), 'strlen');
            foreach ($select_field as $key => $item) {
                if ($select_value[$key] != '_all') {
                    $map[$item] = $select_value[$key];
                }
            }
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[$filter_time] = ['between time', [$filter_time_from.' 00:00:00', $filter_time_to.' 23:59:59']];
        }

        // 表头筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                if (isset($filter_content[$key])) {
                    $map[$item] = ['in', $filter_content[$key]];
                }
            }
        }

        // 搜索区域
        if ($search_area != '') {
            $search_area = explode('|', $search_area);
            $search_area_op = explode('|', $search_area_op);
            foreach ($search_area as $key => $item) {
                list($field, $value) = explode('=', $item);
                $op = explode('=', $search_area_op[$key]);
                if ($value != '') {
                    switch ($op[1]) {
                        case 'like':
                            $map[$field] = ['like', "%$value%"];
                            break;
                        case 'between time':
                        case 'not between time':
                            $value = explode(' - ', $value);
                        default:
                            $map[$field] = [$op[1], $value];
                    }
                }
            }
        }
        return $map;
    }

    /**
     * 获取字段排序
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    final protected function getOrder($extra_order = '', $before = false)
    {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order. ' '. $by;
        }
        if ($before) {
            return $extra_order. ',' .$order. ' '. $by;
        } else {
            return $order. ' '. $by . ',' . $extra_order;
        }
    }

    /**
     * 渲染插件模板
     * @param string $template 模板名称
     * @param string $suffix 模板后缀
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    final protected function pluginView($template = '', $suffix = '', $vars = [], $replace = [], $config = [])
    {
        $plugin_name = input('param.plugin_name');

        if ($plugin_name != '') {
            $plugin = $plugin_name;
            $action = 'index';
        } else {
            $plugin = input('param._plugin');
            $action = input('param._action');
        }
        $suffix = $suffix == '' ? 'html' : $suffix;
        $template = $template == '' ? $action : $template;
        $template_path = config('plugin_path'). "{$plugin}/view/{$template}.{$suffix}";
        return parent::fetch($template_path, $vars, $replace, $config);
    }


    /**
     * 上传附件
     * @param string $dir 保存的目录:images,files,videos,voices
     * @param string $from 来源，wangeditor：wangEditor编辑器, ueditor:ueditor编辑器, editormd:editormd编辑器等
     * @param string $module 来自哪个模块
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function upload($dir = 'images', $from = '', $module = '')
    {
        // 临时取消执行时间限制
        set_time_limit(0);
        if ($dir == '') $this->error('没有指定上传目录');
        if ($from == 'ueditor') return $this->ueditor();
        if ($from == 'jcrop') return $this->jcrop();
        return $this->saveFile($dir, $from, $module);
    }

    /**
     * 保存附件
     * @param string $dir 附件存放的目录
     * @param string $from 来源
     * @param string $module 来自哪个模块
     * @author 蔡伟明 <314013107@qq.com>
     * @return string|\think\response\Json
     */
    private function saveFile($dir = '', $from = '', $module = '')
    {
        // 附件大小限制
        $size_limit = $dir == 'images' ? config('upload_image_size') : config('upload_file_size');
        $size_limit = $size_limit * 1024;
        // 附件类型限制
        $ext_limit = $dir == 'images' ? config('upload_image_ext') : config('upload_file_ext');
        $ext_limit = $ext_limit != '' ? parse_attr($ext_limit) : '';

        // 获取附件数据
        switch ($from) {
            case 'editormd':
                $file_input_name = 'editormd-image-file';
                break;
            case 'ckeditor':
                $file_input_name = 'upload';
                $callback = $this->request->get('CKEditorFuncNum');
                break;
            default:
                $file_input_name = 'file';
        }

        $file = $this->request->file($file_input_name);
//        var_dump($file);
//        die;

        // 判断附件是否已存在
        if ($file_exists = AttachmentModel::get(['md5' => $file->hash('md5')])) {
            $file_path = PUBLIC_PATH. $file_exists['path'];
            switch ($from) {
                case 'wangeditor':
                    return $file_path;
                    break;
                case 'ueditor':
                    return json([
                        "state" => "SUCCESS",          // 上传状态，上传成功时必须返回"SUCCESS"
                        "url"   => $file_path, // 返回的地址
                        "title" => $file_exists['name'], // 附件名
                    ]);
                    break;
                case 'editormd':
                    return json([
                        "success" => 1,
                        "message" => '上传成功',
                        "url"     => $file_path,
                    ]);
                    break;
                case 'ckeditor':
                    return ck_js($callback, $file_path);
                    break;
                default:
                    return json([
                        'code'   => 1,
                        'info'   => '上传成功',
                        'class'  => 'success',
                        'id'     => $file_exists['id'],
                        'path'   => $file_path
                    ]);
            }
        }

        // 判断附件大小是否超过限制
        if ($size_limit > 0 && ($file->getInfo('size') > $size_limit)) {
            switch ($from) {
                case 'wangeditor':
                    return "error|附件过大";
                    break;
                case 'ueditor':
                    return json(['state' => '附件过大']);
                    break;
                case 'editormd':
                    return json(["success" => 0, "message" => '附件过大']);
                    break;
                case 'ckeditor':
                    return ck_js($callback, '', '附件过大');
                    break;
                default:
                    return json([
                        'code'   => 0,
                        'class'  => 'danger',
                        'info'   => '附件过大'
                    ]);
            }
        }

        // 判断附件格式是否符合
        $file_name = $file->getInfo('name');
        $file_ext  = strtolower(substr($file_name, strrpos($file_name, '.')+1));
        $error_msg = '';
        if ($ext_limit == '') {
            $error_msg = '获取文件信息失败！';
        }
        if ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html') {
            $error_msg = '禁止上传非法文件！';
        }
        if (!in_array($file_ext, $ext_limit)) {
            $error_msg = '附件类型不正确！';
        }
        if ($error_msg != '') {
            switch ($from) {
                case 'wangeditor':
                    return "error|{$error_msg}";
                    break;
                case 'ueditor':
                    return json(['state' => $error_msg]);
                    break;
                case 'editormd':
                    return json(["success" => 0, "message" => $error_msg]);
                    break;
                case 'ckeditor':
                    return ck_js($callback, '', $error_msg);
                    break;
                default:
                    return json([
                        'code'   => 0,
                        'class'  => 'danger',
                        'info'   => $error_msg
                    ]);
            }
        }

        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move(config('upload_path') . DS . $dir);

        if($info){
            // 水印功能
            if ($dir == 'images' && config('upload_thumb_water') == 1 && config('upload_thumb_water_pic') > 0) {
                $this->create_water($info->getRealPath());
            }

            // 缩略图路径
            $thumb_path_name = '';
            // 生成缩略图
            if ($dir == 'images' && config('upload_image_thumb') != '') {
                $thumb_path_name = $this->create_thumb($info, $info->getPathInfo()->getfileName(), $info->getFilename());
            }

            // 获取附件信息
            $file_info = [
                'uid'    => session('user_auth.uid'),
                'name'   => $file->getInfo('name'),
                'mime'   => $file->getInfo('type'),
                'path'   => 'uploads/' . $dir . '/' . str_replace('\\', '/', $info->getSaveName()),
                'ext'    => $info->getExtension(),
                'size'   => $info->getSize(),
                'md5'    => $info->hash('md5'),
                'sha1'   => $info->hash('sha1'),
                'thumb'  => $thumb_path_name,
                'module' => $module
            ];

            // 写入数据库
            if ($file_add = AttachmentModel::create($file_info)) {
                $file_path = PUBLIC_PATH. $file_info['path'];
                switch ($from) {
                    case 'wangeditor':
                        return $file_path;
                        break;
                    case 'ueditor':
                        return json([
                            "state" => "SUCCESS",          // 上传状态，上传成功时必须返回"SUCCESS"
                            "url"   => $file_path, // 返回的地址
                            "title" => $file_info['name'], // 附件名
                        ]);
                        break;
                    case 'editormd':
                        return json([
                            "success" => 1,
                            "message" => '上传成功',
                            "url"     => $file_path,
                        ]);
                        break;
                    case 'ckeditor':
                        return ck_js($callback, $file_path);
                        break;
                    default:
                        return json([
                            'code'   => 1,
                            'info'   => '上传成功',
                            'class'  => 'success',
                            'id'     => $file_add['id'],
                            'path'   => $file_path
                        ]);
                }
            } else {
                switch ($from) {
                    case 'wangeditor':
                        return "error|上传失败";
                        break;
                    case 'ueditor':
                        return json(['state' => '上传失败']);
                        break;
                    case 'editormd':
                        return json(["success" => 0, "message" => '上传失败']);
                        break;
                    case 'ckeditor':
                        return ck_js($callback, '', '上传失败');
                        break;
                    default:
                        return json(['code' => 0, 'class' => 'danger', 'info' => '上传失败']);
                }
            }
        }else{
            switch ($from) {
                case 'wangeditor':
                    return "error|".$file->getError();
                    break;
                case 'ueditor':
                    return json(['state' => '上传失败']);
                    break;
                case 'editormd':
                    return json(["success" => 0, "message" => '上传失败']);
                    break;
                case 'ckeditor':
                    return ck_js($callback, '', '上传失败');
                    break;
                default:
                    return json(['code' => 0, 'class' => 'danger', 'info' => $file->getError()]);
            }
        }
    }

}