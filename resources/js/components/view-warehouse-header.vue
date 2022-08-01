<template>
  <PageHeader :hidden-breadcrumb="true" title="Warehouse Info">
    <template #content>
      <DescriptionList :col="2">
        <Description term="ID " class="headerRow">{{
          warehouse ? warehouse.id : "-"
        }}</Description>
        <Description term="Name " class="headerRow">{{
          warehouse ? warehouse.name : "-"
        }}</Description>
        <Description term="Location " class="headerRow">{{
          warehouse ? warehouse.location : "-"
        }}</Description>
        <Description term="Warehouse Manager " class="headerRow">{{
          warehouse ? (warehouse.manager ? warehouse.manager.name : "-") : "-"
        }}</Description>
        <Description term="Created by " class="headerRow">{{
          warehouse ? (warehouse.created_at ? warehouse.created_at : "-") : "-"
        }}</Description>
        <Description term="Updated by " class="headerRow">{{
          warehouse ? warehouse.updated_at : "-"
        }}</Description>
      </DescriptionList>
    </template>
  </PageHeader>
</template>

<script>
export default {
  props: {
    warehouse: Object,
  },
};
</script>