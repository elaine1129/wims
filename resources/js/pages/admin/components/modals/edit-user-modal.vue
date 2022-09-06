<template>
  <Modal v-model="modal" title="Edit User">
    <Form :model="editUserForm" :label-width="80">
      <FormItem label="ID">
        <Input :disabled="true" v-model="editUserForm.id"></Input>
      </FormItem>
      <FormItem label="Name">
        <Input :disabled="true" v-model="editUserForm.name"></Input>
      </FormItem>
      <FormItem label="Email">
        <Input v-model="editUserForm.email" placeholder="Enter email"></Input>
      </FormItem>
      <FormItem label="Contact No">
        <Input
          v-model="editUserForm.contact_no"
          type="number"
          placeholder="Enter contact no"
        ></Input>
      </FormItem>
      <FormItem label="IC No">
        <Input :disabled="true" v-model="editUserForm.ic_no"></Input>
      </FormItem>
      <FormItem label="Role">
        <Select v-model="editUserForm.role">
          <Option v-for="role in this.roles" :key="role" :value="role">{{
            role
          }}</Option>
        </Select>
      </FormItem>
      <FormItem label="Warehouse">
        <Select v-model="editUserForm.warehouse.id">
          <Option
            v-for="warehouse in data.warehouses"
            :key="warehouse.id"
            :value="warehouse.id"
            >{{ warehouse.name }}</Option
          >
        </Select>
      </FormItem>
      <FormItem label="Address">
        <Input
          v-model="editUserForm.address"
          type="textarea"
          placeholder="Enter address"
        ></Input>
      </FormItem>
      <FormItem label="Username">
        <Input :disabled="true" v-model="editUserForm.username"></Input>
      </FormItem>
    </Form>

    <template #footer>
      <Button @click="closeEditUserModal">Cancel</Button>
      <Button type="primary" @click="updateUser">Confirm</Button>
    </template>
  </Modal>
</template>

<script>
export default {
  data() {
    return {
      data: {
        warehouses: [],
      },
      modal: false,
      editUserForm: {
        id: "",
        name: "",
        email: "",
        contact_no: "",
        ic_no: "",
        role: "",
        warehouse: {
          id: "",
        },
        address: "",
        username: "",
      },
    };
  },
  async created() {
    await this.$axiosClient
      .get("/warehouses")
      .then((response) => {
        console.log(response);
        this.data.warehouses = response.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
  },
  methods: {
    setProps(modal, selectedUser) {
      this.modal = modal;
      this.editUserForm = _.cloneDeep(selectedUser);
      console.log(selectedUser);
    },
    closeEditUserModal() {
      this.modal = false;
    },

    async updateUser() {
      let params = {
        email: this.editUserForm.email,
        contact_no: this.editUserForm.contact_no,
        role: this.editUserForm.role,
        warehouse_id: this.editUserForm.warehouse.id,
        address: this.editUserForm.address,
      };
      await this.$axiosClient
        .put("/user/" + this.editUserForm.id, params)
        .then((response) => {
          this.editUserForm = {
            id: "",
            name: "",
            email: "",
            contact_no: "",
            ic_no: "",
            role: "",
            warehouse: {
              id: "",
            },
            address: "",
            username: "",
          };
          this.success("User successfully updated");
          this.closeEditUserModal();
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>