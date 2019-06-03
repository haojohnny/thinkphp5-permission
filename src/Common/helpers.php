<?php

if (!function_exists('array_flatten')) {
    /**
     * @param $array
     * @param $depth
     * @return array
     */
    function array_flatten($array, $depth = INF)
    {
        $result = [];
        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } elseif ($depth === 1) {
                $result = array_merge($result, array_values($item));
            } else {
                $result = array_merge($result, array_flatten($item, $depth - 1));
            }
        }

        return $result;
    }
}

if (!function_exists('list2tree')) {
    /**
     * 非递归方式
     * @param $list
     * @param string $pk 数据主键
     * @param string $pid 父id
     * @param string $child 子数据
     * @param int $root 顶级
     * @return array 返回树形结构数组
     */
    function list2tree($list, $pk = 'id', $pid = 'pid', $child = 'child', $root = 0)
    {
        $tree = [];
        if (is_array($list)) {
            // 创建主键引用的参照数组
            $refer = [];
            foreach ($list as $key => $value) {
                $refer[$value[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $value) {
                $parentId = $value[$pid];
                // 判断list数据列表中是否存在$parentId为$root,存在则存放到tree（顶级）
                if ($parentId == $root) {
                    $tree[] =& $list[$key];
                } else {
                    // 如果引用参照数组中存在key为$parentId的value值
                    if (isset($refer[$parentId])) {
                        // 将该值的引用地址赋值给$parent
                        $parent =& $refer[$parentId];
                        if (!array_key_exists($child, $parent)) {
                            // 为$parent创建key为$child的子,用来存放子数据
                            $parent[$child] = [];
                        }
                        if (!array_key_exists($child . '_id', $parent)) {
                            // 为$parent创建key为$child.'_id'的子,用来存放子id
                            $parent[$child . '_id'] = '';
                        }
                        // 存该条数据的引用地址
                        $parent[$child][] =& $list[$key];
                        // 存该条数据的主键id，逗号分隔
                        $parent[$child . '_id'] .= $list[$key][$pk] . ',';
                    }
                }
            }
        }

        return $tree;
    }
}

