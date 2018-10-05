import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

export default ({ app, store }) => {
  // Set i18n instance on app
  // This way we can use it in middleware and pages asyncData/fetch
  app.i18n = new VueI18n({
    locale: store.state.locale,
    fallbackLocale: 'en',
    silentTranslationWarn: true,
    messages: {
      "en": {
        "default": {
          "list": "List",
          "add": "Add",
          "import": "Import",
          "previousPage": "Previous",
          "nextPage": "Next",
          "page": "Page",
          "changePassword": "Change password",
          "logout": "Logout",
          "search": "Search",
          "all": "All",
          "filter": "Filter",
          "reset": "Reset",
          "selectAction": "Select action",
          "submit": "Submit",
          "backToHome": "Back to Home",
          "forbidden": "Forbidden",
          "pageNotFound": "Page not found",
          "noContent": "No content",
          "delete": "Delete",
          "update": "Update",
          "download": "Download",
          "upload": "Upload",
          "downloading": "Download",
          "notify": "Notify",
          "refresh": "Refresh",
          "cancel": "Cancel",
          "warning": "Warning",
          "updateSearchEngine": "Update Search Engine",
          "msg": {
            "deleteConfirm": "Are you sure to delete?",
            "confirm": "Yes",
            "cancel": "No",
            "deleteSuccess": "successfully",
            "noActionChosen": "No action chosen",
            "noItemSelected": "No item selected"
          }
        }
      },
      "vi": {
        "default": {
          "list": "Danh sách",
          "add": "Thêm",
          "import": "Import",
          "previousPage": "Trước",
          "nextPage": "Sau",
          "page": "Trang",
          "changePassword": "Đổi mật khẩu",
          "logout": "Thoát",
          "search": "Tìm kiếm",
          "all": "Tất cả",
          "filter": "Lọc",
          "reset": "Reset",
          "selectAction": "Chọn tác vụ",
          "submit": "Thực thi",
          "backToHome": "Trở về Trang chủ",
          "forbidden": "Không có quyền truy cập",
          "pageNotFound": "Không tìm thấy trang",
          "noContent": "Chưa có dữ liệu",
          "delete": "Xoá",
          "update": "Cập nhật",
          "download": "Tải xuống",
          "upload": "Tải lên",
          "downloading": "Đang tải",
          "notify": "Thông báo",
          "refresh": "Làm mới",
          "cancel": "Huỷ",
          "warning": "Cảnh báo",
          "updateSearchEngine": "Cập nhật máy tìm kiếm",
          "msg": {
            "deleteConfirm": "Bạn chắc muốn xoá?",
            "confirm": "Có",
            "cancel": "Không",
            "deleteSuccess": "thành công",
            "noActionChosen": "Chưa chọn tác vụ",
            "noItemSelected": "Chưa chọn đối tượng"
          }
        }
      }
    }
  });
}
