<template>
  <PageComponent title="Manage User">
    <Button
      type="primary"
      icon="ios-add-circle-outline"
      style="margin-bottom: 20px"
      @click="showAddUserModal"
      >Add</Button
    >

    <Modal v-model="addUserModal" title="Add new user">
      <Form :model="addUserForm" :label-width="80">
        <FormItem label="Name">
          <Input
            v-model="addUserForm.name"
            placeholder="Enter user name"
          ></Input>
        </FormItem>
        <FormItem label="Email">
          <Input
            v-model="addUserForm.email"
            placeholder="Enter email (eg.abc@wims.com)"
          ></Input>
        </FormItem>
        <FormItem label="Contact No">
          <Input
            v-model="addUserForm.contact_no"
            placeholder="Enter contact no. (eg.0123456789)"
            type="number"
          ></Input>
        </FormItem>
        <FormItem label="IC No">
          <Input
            v-model="addUserForm.ic_no"
            placeholder="Enter IC no. (eg. 123456-12-1234)"
          ></Input>
        </FormItem>
        <FormItem label="Role">
          <Select v-model="addUserForm.role">
            <Option v-for="role in this.roles" :key="role" :value="role">{{
              role
            }}</Option>
          </Select>
        </FormItem>
        <FormItem label="Warehouse">
          <Select v-model="addUserForm.warehouse_id">
            <Option
              v-for="warehouse in data.warehouses"
              :key="warehouse.id"
              :value="warehouse.id"
              >{{ warehouse.name }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="Employed in">
          <DatePicker
            type="date"
            placeholder="Select date"
            v-model="addUserForm.employed_in"
          ></DatePicker>
        </FormItem>
        <FormItem label="Address">
          <Input
            v-model="addUserForm.address"
            type="textarea"
            placeholder="Enter address"
          ></Input>
        </FormItem>
        <FormItem label="Username">
          <Input
            v-model="addUserForm.username"
            placeholder="Enter username"
          ></Input>
        </FormItem>
      </Form>
      <template #footer>
        <Button @click="closeAddUserModal">Cancel</Button>
        <Button type="primary" @click="addNewUser">Confirm</Button>
      </template>
    </Modal>
    <ViewUserTableComponent
      name="users"
      :data="data.users"
      @edited="editedInChild"
      @deleted="deletedInChild"
    ></ViewUserTableComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import ViewUserTableComponent from "../../components/tables/view-user-table.vue";
import router from "../../router";
export default {
  components: {
    PageComponent,
    ViewUserTableComponent,
  },
  watch: {
    "data.users": {
      handler() {
        var table = $("#users").DataTable({
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
        users: [],
        warehouses: [],
      },
      newUser: null,
      addUserModal: false,
      addUserForm: {
        name: "",
        email: "",
        contact_no: "",
        ic_no: "",
        role: "",
        warehouse_id: null,
        employed_in: "",
        address: "",
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
      .get("/users")
      .then((res) => {
        this.data.users = res.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });

    // $(document).ready(function () {
    //   $("#users").DataTable();
    // });
  },
  methods: {
    async refetchData() {
      await this.$axiosClient
        .get("/users")
        .then((res) => {
          this.data.users = res.data.data;

          $("#users").DataTable().destroy();
        })
        .catch((error) => {
          console.log(error);
          this.handleApiError(error);
        });
    },
    async addNewUser() {
      console.log(this.addUserForm);

      await this.$axiosClient
        .post("/user", this.addUserForm)
        .then((response) => {
          console.log(response);
          this.addUserForm = {
            name: "",
            email: "",
            contact_no: "",
            ic_no: "",
            role: "",
            warehouse_id: null,
            employed_in: "",
            address: "",
          };
          this.success("User Added!");
          this.addUserModal = false;
          this.refetchData();
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    closeAddUserModal() {
      this.addUserForm = {
        name: "",
        email: "",
        contact_no: "",
        ic_no: "",
        role: "",
        warehouse_id: null,
        employed_in: "",
        address: "",
      };
      this.addUserModal = false;
    },
    async showAddUserModal() {
      await this.$axiosClient
        .get("/warehouses")
        .then((response) => {
          this.data.warehouses = response.data.data;
          this.addUserModal = true;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
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