<template>
  <PageComponent title="Manage Category">
    <Button
      type="primary"
      icon="ios-add-circle-outline"
      style="margin-bottom: 20px"
      @click="showAddCategoryModal"
      >Add</Button
    >

    <Modal v-model="addCategoryModal" title="Add new category">
      <Form :model="addCategoryForm" :label-width="80">
        <FormItem label="Name">
          <Input
            v-model="addCategoryForm.name"
            placeholder="Enter category name"
          ></Input>
        </FormItem>
      </Form>
      <template #footer>
        <Button @click="closeAddCategoryModal">Cancel</Button>
        <Button type="primary" @click="addNewCategory">Confirm</Button>
      </template>
    </Modal>
    <table id="categories" class="display" style="width: 100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Category Name</th>
          <th>Created At</th>
          <th>Updated At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in data.categories" :key="category.id">
          <td>{{ category.id }}</td>
          <td>{{ category.name }}</td>
          <td>
            {{ category.created_at ? convertDate(category.created_at) : "-" }}
          </td>
          <td>
            {{ category.updated_at ? convertDate(category.updated_at) : "-" }}
          </td>
          <td>
            <div class="flex items-center gap-x-3">
              <Button
                type="success"
                class="basis-1/2"
                @click="showEditCategoryModal(category)"
                >Edit</Button
              >
              <Button
                type="error"
                class="basis-1/2"
                @click="showDeleteCategoryModal(category)"
                >Delete</Button
              >
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <EditCategoryModalComponent
      ref="editCategoryModalComponent"
      @edited="editedInChild"
    ></EditCategoryModalComponent>
    <DeleteCategoryModalComponent
      ref="deleteCategoryModalComponent"
      @deleted="deletedInChild"
    ></DeleteCategoryModalComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import EditCategoryModalComponent from "../admin/components/modals/edit-category-modal.vue";
import DeleteCategoryModalComponent from "../admin/components/modals/delete-category-modal.vue";
import router from "../../router";
export default {
  components: {
    PageComponent,
    EditCategoryModalComponent,
    DeleteCategoryModalComponent,
  },
  watch: {
    "data.categories": {
      handler() {
        var table = $("#categories").DataTable({
          drawCallback: function (settings) {
            var api = new $.fn.dataTable.Api(settings);
            if (api.page() >= 0) {
              router.push({ query: { page: api.page() + 1 } });
            }
          },
        });
        table.page(this.$route.query.page - 1).draw(false);
      },
      deep: true,
      flush: "post",
    },
  },
  data() {
    return {
      data: {
        categories: [],
      },
      addCategoryModal: false,
      addCategoryForm: {
        name: "",
      },
      selectedCategory: null,
      editCategoryModal: false,
      editCategoryForm: {
        name: "",
      },
    };
  },
  beforeCreate() {
    if (!this.$route.query.page > 0) {
      router.push({ query: { page: 1 } });
    }
  },
  async created() {
    await this.$axiosClient
      .get("/categories")
      .then((res) => {
        this.data.categories = res.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    // $(document).ready(function () {
    //   $("#categories").DataTable();
    // });
  },
  methods: {
    async refetchData() {
      await this.$axiosClient
        .get("/categories")
        .then((res) => {
          this.data.categories = res.data;

          $("#categories").DataTable().destroy();
        })
        .catch((error) => {
          console.log(error);
          this.handleApiError(error);
        });
    },
    async addNewCategory() {
      console.log(this.addCategoryForm);
      await this.$axiosClient
        .post("/category", this.addCategoryForm)
        .then((response) => {
          console.log(response);
          this.addCategoryForm = {
            name: "",
          };
          this.success("Category Created!");
          this.addCategoryModal = false;
          this.refetchData();
        })
        .catch((error) => {
          console.log(error.response);
          this.handleApiError(error);
        });
    },
    closeAddCategoryModal() {
      this.addCategoryForm = {
        name: "",
      };
      this.addCategoryModal = false;
    },
    showAddCategoryModal() {
      this.addCategoryModal = true;
    },

    showEditCategoryModal(category) {
      this.selectedCategory = category;
      this.$refs.editCategoryModalComponent.setProps(
        true,
        this.selectedCategory
      );
    },
    editCategory() {
      console.log(this.editCategoryForm);
    },
    showDeleteCategoryModal(category) {
      this.selectedCategory = category;
      this.$refs.deleteCategoryModalComponent.setProps(
        true,
        this.selectedCategory
      );
    },
    editedInChild() {
      this.refetchData();
    },
    deletedInChild() {
      this.refetchData();
    },
  },
};
</script>