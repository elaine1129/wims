<template>
  <Modal v-model="modal" title="Edit Warehouse">
    <Form :model="editCategoryForm" :label-width="80">
      <FormItem label="ID">
        <Input :disabled="true" v-model="editCategoryForm.id"></Input>
      </FormItem>
      <FormItem label="Name">
        <Input v-model="editCategoryForm.name"></Input>
      </FormItem>
    </Form>

    <template #footer>
      <Button>Cancel</Button>
      <Button type="primary" @click="editCategory">Confirm</Button>
    </template>
  </Modal>
</template>

<script>
export default {
  data() {
    return {
      data: {
        staff_candidates: [],
      },
      modal: false,
      editCategoryForm: {
        id: "",
        name: "",
      },
    };
  },
  created() {},
  methods: {
    async setProps(modal, selectedCategory) {
      this.editCategoryForm = selectedCategory;
      this.modal = modal;
    },
    async editCategory() {
      await this.$axiosClient
        .put("/category/" + this.editCategoryForm.id, this.editCategoryForm)
        .then((response) => {
          this.editCategoryForm = {
            id: "",
            name: "",
          };
          this.success("The category has been successfully updated.");
          this.closeUpdateCategoryModal();
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    closeUpdateCategoryModal() {
      this.modal = false;
    },
  },
};
</script>