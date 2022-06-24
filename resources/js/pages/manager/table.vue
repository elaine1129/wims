<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>SKU ID</th>
        <th>Inventory ID</th>
        <th>Inventory Name</th>
        <th>Storage Number</th>
        <th>Schedule</th>
        <th>Days due</th>
        <th>Staff assigned</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="schedule in data" :key="schedule.id">
        <td>{{ schedule.sku.id }}</td>
        <td>{{ schedule.sku.inventory.id }}</td>
        <td>{{ schedule.sku.inventory.name }}</td>
        <td>{{ schedule.storage_bin[0].bin_number }}</td>
        <td>{{ schedule.schedule ? schedule.schedule : "-" }}</td>
        <td>
          {{
            schedule.schedule
              ? Math.ceil(
                  (new Date(schedule.schedule) - new Date()) /
                    (1000 * 3600 * 24)
                )
              : "-"
          }}
        </td>
        <td>{{ schedule.staff.name }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
const props = defineProps({
  data: Array,
  name: String,
});
</script>