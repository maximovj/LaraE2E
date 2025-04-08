export default {
    install(app) {
        app.config.globalProperties.$hasRole = function (roleName) {
            const user = this.$page.props.auth.user;
            return user?.roles?.includes(roleName);
        };

        app.config.globalProperties.$hasPermission = function (permissionName) {
            const user = this.$page.props.auth.user;
            return user?.permissions?.includes(permissionName);
        };
    }
};
