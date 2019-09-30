<?php

/**
 * 节点
 * Class Node
 */
class Node
{
    public $d; //广度搜索时表示距离，深度搜索表示变灰色时的时间戳
    public $color;//色彩
    public $p;//父节点
    public $val; //节点值
    public $time; //变黑色时的时间戳
    public $linkNodes = [];//连接的节点， 无向的

    public function __construct($val)
    {
        $this->val = $val; // 几点内容，类似 a b c d
    }
}

/**
 * 无向图
 * Class Graph
 */
class Graph
{

    /**
     * 建图
     * @param array $data 数组
     */
    public function buildGraph($data)
    {
        $nodeVals = array_keys($data); // 节点

        $nodes = []; // 几点的集合
        foreach ($nodeVals as $nodeVal) {
            $nodes[$nodeVal] = new Node($nodeVal);
        }
        foreach ($data as $key => $vals) {
            foreach ($vals as $val) {
                if (isset($nodes[$val])) {
                    $nodes[$key]->linkNodes[] = $nodes[$val];
                }
            }
        }
        return $nodes;
    }

    /**
     * 广度搜索
     * @param array $graphNodes
     * @param Node $node
     */
    public function BFS($graphNodes, $node)
    {


        $result = [];
        foreach ($graphNodes as $item) {
            $item->color = 'WHITE';
            $item->d     = 0; // 深度遍历举例
            $item->p     = null; // 深度遍历父节点
        }
        $node->color = 'GRAY';
        $node->p     = NULL;
        $node->d     = 0;
        $queue       = [];
        array_push($queue, $node);
        while (!empty($queue)) {
            $u = array_shift($queue); // 删除 a 节点
            foreach ($u->linkNodes as $linkNode) {
                if ($linkNode->color == 'WHITE') {
                    $linkNode->color = 'GRAY';
                    $linkNode->d     = $u->d + 1;
                    $linkNode->p     = $u;
                    array_push($queue, $linkNode); // 入b que, f 入que
                }
            }

            $u->color = 'BLACK'; // a 节点遍历完了，变黑了
            array_push($result, $u->val); //
        }
        return $result;
    }

    /**
     * 深度搜索
     * @param array $graphNodes
     */
    public function DFS($graphNodes)
    {
        $result = [];
        foreach ($graphNodes as $graphNode) {
            $graphNode->color = 'WHITE';
            $graphNode->p     = NULL;
        }
        $time = 0;
        foreach ($graphNodes as $graphNode) {
            if ($graphNode->color == 'WHITE') {
                $this->DFSVisit($graphNode, $time, $result);
            }
        }
        return $result;
    }

    /**
     * 深度遍历节点
     * @param Node $u
     */
    public function DFSVisit($u, $time, &$result)
    {
        $result[] = $u;
        $u->color = 'GRAY';
        $time     = $time + 1;
        $u->d     = $time;
        foreach ($u->linkNodes as $linkNode) {
            if ($linkNode->color == 'WHITE') {
                $linkNode->p = $u;
                $this->DFSVisit($linkNode, $time, $result);
            }
        }
        $u->color = 'BLACK';
        $u->time  = $time + 1;
    }

}

$data = [
    'a' => ['b', 'f'],
    'b' => ['a', 'c', 'd'], // 连接的点，无向图
    'c' => ['b', 'd', 'e'],
    'd' => ['b', 'c'],
    'e' => ['c'],
    'f' => ['a', 'g', 'h'],
    'g' => ['f', 'h'],
    'h' => ['f', 'g']
];

$obj        = new Graph();
$graphNodes = $obj->buildGraph($data);
//广度优先遍历结果
$bfsResult = $obj->BFS($graphNodes, current($graphNodes)); // 从a开始遍历
var_dump($bfsResult);exit;

//深度优先遍历结果
$dfsResult = $obj->DFS($graphNodes);