<template>
  <Modal v-model="modal" title="Delete category">
    <h2>Are you sure to delete category "{{ selectedCategory.name }}"?</h2>
    <template #footer>
      <Button @click="closeDeleteCategoryModal">Cancel</Button>
      <Button type="error" @click="deleteCategory">Delete</Button>
    </template>
  </Modal>
</template>

<script>
import router from "../../../../router";

export default {
  data() {
    return {
      modal: false,
      selectedCategory: {
        id: "",
        name: "",
      },
    };
  },
  created() {},
  methods: {
    setProps(modal, selectedCategory) {
      this.modal = modal;
      this.selectedCategory = selectedCategory;
      console.log(this.selectedCategory);
    },
    closeDeleteCategoryModal() {
      this.modal = false;
      this.selectedCategory = {
        id: "",
        name: "",
      };
    },
    async deleteCategory() {
      await this.$axiosClient
        .delete("/category/" + this.selectedCategory.id)
        .then((response) => {
          this.success("Category successfully deleted!");
          this.modal = false;
          if (this.$route.name == "admin-manage-category") {
            this.$emit("deleted");
          } else {
            router.go(-1);
          }
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>