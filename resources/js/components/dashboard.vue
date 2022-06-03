<template>
    <div id = "app">
         <div class="_1side_menu_logo">
            <h3 style="text-align: center">{{ headerTitle }}</h3>
            <!--<img src="/img/logo.jpg" style="width: 108px;margin-left: 68px;"/>-->
        </div>
        <Menu mode="horizontal" :theme="theme" active-name="1">
            <MenuItem name="1" to="/staff-check-in-out-stock">
                <Icon type="ios-paper" />
                Check In/Out Stock
            </MenuItem>
            <MenuItem name="2">
                <Icon type="ios-people" />
                用户管理
            </MenuItem>
            <Submenu name="3">
                <template #title>
                    <Icon type="ios-stats" />
                    统计分析
                </template>
                <MenuGroup title="使用">
                    <MenuItem name="3-1">新增和启动</MenuItem>
                    <MenuItem name="3-2">活跃分析</MenuItem>
                    <MenuItem name="3-3">时段分析</MenuItem>
                </MenuGroup>
                <MenuGroup title="留存">
                    <MenuItem name="3-4">用户留存</MenuItem>
                    <MenuItem name="3-5">流失用户</MenuItem>
                </MenuGroup>
            </Submenu>
            <MenuItem name="4">
                <Icon type="ios-construct" />
                综合设置
            </MenuItem>
        </Menu>
        <router-view />
    </div>
</template>
<script>
    export default {
        data () {
            return {
                theme: 'primary',
                headerTitle:''
            }
        },
        methods:{
            setHeaderTitle(title){
                this.headerTitle = title
            }

        },
        created(){
            //read logged in staff's warehouse name
            let title = 'Check In/Out Stock - ' + 'WAREHOUSE NAME'
            this.setHeaderTitle(title)
        },
        
    };
</script>

