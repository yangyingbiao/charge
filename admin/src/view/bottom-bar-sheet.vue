<template>
    <div class='x-sheet'>
        <slot></slot>
        <footer class='x-bottom-bar'>
            <slot name='bar'></slot>
            <!-- <el-button size='small' type='primary'>确定</el-button>
            <el-button size='small' >返回</el-button> -->
        </footer>
    </div>
</template>

<style scoped lang='scss'>
    .x-sheet{
        height: 100%;
        position: relative;
    }

    .x-bottom-bar{
        position: sticky;
        bottom: 0;
        box-shadow: 0px -2px 10px 2px #f3f2f2;
        padding: 10px 0;
        text-align: center;
    }
</style>