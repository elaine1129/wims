<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>Inventory ID</th>
        <th>Name</th>
        <th v-if="this.$store.getters.getUser.role == 'Admin'">Warehouse</th>
        <th>Cost Per Unit</th>
        <th>Quantity On Hand</th>
        <!-- <th>Storage Bin Number</th> -->
        <th>Created By</th>
        <th>Updated By</th>
        <th v-if="this.$store.getters.getUser.role == 'Admin'">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="inventory in data" :key="inventory.id">
        <td>
          <router-link
            :to="{
              name: 'view-inventory-details',
              params: { id: inventory.id },
            }"
            >{{ inventory.id }}</router-link
          >
        </td>
        <td>{{ inventory.name }}</td>
        <td v-if="this.$store.getters.getUser.role == 'Admin'">
          {{ inventory.warehouse.name }}
        </td>
        <td>{{ inventory.cost_per_unit }}</td>
        <td>{{ inventory.qty_on_hand }}</td>
        <!-- <td>{{ inventory.storage_bin[0].bin_number }}</td> -->
        <td>{{ inventory.created_at ? inventory.created_at : "-" }}</td>
        <td>{{ inventory.updated_at ? inventory.updated_at : "-" }}</td>
        <td v-if="this.$store.getters.getUser.role == 'Admin'">
          <div class="flex items-center gap-x-3">
            <Button
              type="success"
              class="basis-1/2"
              @click="showEditInventoryModal(inventory)"
              >Edit</Button
            >
            <Button
              type="error"
              class="basis-1/2"
              @click="showDeleteInventoryModal(inventory)"
              >Delete</Button
            >
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <EditInventoryModalComponent
    ref="editInventoryModalComponent"
  ></EditInventoryModalComponent>
  <DeleteInventoryModalComponent
    ref="deleteInventoryModalComponent"
    @deleted="deletedInChild"
  ></DeleteInventoryModalComponent>
</template>

<script>
import EditInventoryModalComponent from "../../pages/admin/components/edit-inventory-modal.vue";
import DeleteInventoryModalComponent from "../../pages/admin/components/delete-inventory-modal.vue";
import router from "../../router";
export default {
  props: {
    data: Array,
    name: String,
  },
  computed: {
    myData: {
      get() {
        return this.data;
      },
      set(v) {
        this.$emit("setData", v);
      },
    },
  },
  components: {
    EditInventoryModalComponent,
    DeleteInventoryModalComponent,
  },
  data() {
    return {
      selectedInventory: null,
    };
  },
  methods: {
    showEditInventoryModal(inventory) {
      this.selectedInventory = inventory;
      this.$refs.editInventoryModalComponent.setProps(
        true,
        this.selectedInventory
      );
    },
    showDeleteInventoryModal(inventory) {
      this.selectedInventory = inventory;
      this.$refs.deleteInventoryModalComponent.setProps(
        true,
        this.selectedInventory
      );
    },
    showInventoryDetailPage(inventory) {
      router.push(`/view-inventory-details/${inventory.id}`);
    },
    deletedInChild(id) {
      this.myData = _.remove(this.data, { id: id });
    },
  },
};
</script>