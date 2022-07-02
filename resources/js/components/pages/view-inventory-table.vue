<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>Inventory ID</th>
        <th>Name</th>
        <th>Cost Per Unit</th>
        <th>Quantity On Hand</th>
        <th>Storage Bin Number</th>
        <th>Created By</th>
        <th>Updated By</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="inventory in data" :key="inventory.id">
        <td>{{ inventory.id }}</td>
        <td>{{ inventory.name }}</td>
        <td>{{ inventory.cost_per_unit }}</td>
        <td>{{ inventory.qty_on_hand }}</td>
        <td>{{ inventory.storage_bin[0].bin_number }}</td>
        <td>{{ inventory.created_at ? inventory.created_at : "-" }}</td>
        <td>{{ inventory.updated_at ? inventory.updated_at : "-" }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    data: Array,
    name: String,
  },
};
</script>