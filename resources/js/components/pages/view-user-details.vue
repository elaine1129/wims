<template>
  <PageComponent title="View user">
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">ID</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.id : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Name</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.name : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Email</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.email : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Contact No</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.contact_no : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">IC No</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.ic_no : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Role</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.role : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Warehouse</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.warehouse.name : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Employed In</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.employed_in : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Address</div>
      <div class="col-span-2">
        {{ data.selectedUser ? data.selectedUser.address : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Created At</div>
      <div class="col-span-2">
        {{
          data.selectedUser
            ? data.selectedUser.created_at
              ? data.selectedUser.created_at
              : "-"
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="user-details-label">Updated At</div>
      <div class="col-span-2">
        {{
          data.selectedUser
            ? data.selectedUser.updated_at
              ? data.selectedUser.updated_at
              : "-"
            : "-"
        }}
      </div>
    </div>
    <div class="flex justify-center space-x-4">
      <Button
        type="success"
        size="large"
        ghost
        class="w-24"
        @click="showEditUserModal"
        >Edit</Button
      >
      <Button
        type="error"
        size="large"
        ghost
        class="w-24"
        @click="showDeleteUserModal"
        >Delete</Button
      >
    </div>
    <EditUserModalComponent
      ref="editUserModalComponent"
    ></EditUserModalComponent>
    <DeleteUserModalComponent
      ref="deleteUserModalComponent"
    ></DeleteUserModalComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../pages/default-page.vue";
import EditUserModalComponent from "../../pages/admin/components/modals/edit-user-modal.vue";
import DeleteUserModalComponent from "../../pages/admin/components/modals/delete-user-modal.vue";

export default {
  components: {
    PageComponent,
    EditUserModalComponent,
    DeleteUserModalComponent,
  },
  data() {
    return {
      data: {
        selectedUser: null,
      },
    };
  },
  async created() {
    console.log(this.$route.params);
    await this.$axiosClient
      .get("/user/" + this.$route.params.id)
      .then((response) => {
        this.data.selectedUser = response.data.data;
        console.log(this.data.selectedUser);
      })
      .catch((error) => {
        this.handleApiError(error);
      });
  },
  methods: {
    showEditUserModal() {
      this.$refs.editUserModalComponent.setProps(true, this.data.selectedUser);
    },
    showDeleteUserModal() {
      this.$refs.deleteUserModalComponent.setProps(
        true,
        this.data.selectedUser
      );
    },
  },
};
</script>