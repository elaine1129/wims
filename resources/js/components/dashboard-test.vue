<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <!--
    This example requires updating your template:

    ```
    <html class="h-full bg-gray-100">
    <body class="h-full">
    ```
  -->

  <div class="tw-min-h-full tw-min-w-full">
    <Disclosure as="nav" class="tw-bg-gray-800" v-slot="{ open }">
      <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 lg:tw-px-8">
        <div class="tw-flex tw-items-center tw-justify-between tw-h-16">
          <div class="tw-flex tw-items-center">
            <div class="tw-flex-shrink-0">
              <img
                class="tw-h-8 tw-w-8"
                src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg"
                alt="Workflow"
              />
            </div>
            <div class="tw-hidden md:tw-block">
              <div class="tw-ml-10 tw-flex tw-items-baseline tw-space-x-4">
                <a
                  v-for="item in navigation"
                  :key="item.name"
                  :href="item.href"
                  :class="[
                    item.current
                      ? 'tw-bg-gray-900 tw-text-white'
                      : 'tw-text-gray-300 hover:tw-bg-gray-700 hover:tw-text-white',
                    'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium',
                  ]"
                  :aria-current="item.current ? 'page' : undefined"
                  >{{ item.name }}</a
                >
              </div>
            </div>
          </div>
          <div class="tw-hidden md:tw-block">
            <div class="tw-ml-4 tw-flex tw-items-center md:tw-ml-6">
              <button
                type="button"
                class="
                  tw-bg-gray-800 tw-p-1 tw-rounded-full tw-text-gray-400
                  hover:
                  tw-text-white
                  focus:
                  tw-outline-none
                  focus:
                  tw-ring-2
                  focus:
                  tw-ring-offset-2
                  focus:
                  tw-ring-offset-gray-800
                  focus:
                  tw-ring-white
                "
              >
                <span class="tw-sr-only">View notifications</span>
                <BellIcon class="tw-h-6 tw-w-6" aria-hidden="true" />
              </button>

              <!-- Profile dropdown -->
              <Menu as="div" class="tw-ml-3 tw-relative">
                <div>
                  <MenuButton
                    class="
                      tw-max-w-xs
                      tw-bg-gray-800
                      tw-rounded-full
                      tw-flex
                      tw-items-center
                      tw-text-sm
                      focus:
                      tw-outline-none
                      focus:
                      tw-ring-2
                      focus:
                      tw-ring-offset-2
                      focus:
                      tw-ring-offset-gray-800
                      focus:
                      tw-ring-white
                    "
                  >
                    <span class="tw-sr-only">Open user menu</span>
                    <img
                      class="tw-h-8 tw-w-8 tw-rounded-full"
                      :src="user.imageUrl"
                      alt=""
                    />
                  </MenuButton>
                </div>
                <transition
                  enter-active-class=" tw-transition  tw-ease-out  tw-duration-100"
                  enter-from-class=" tw-transform  tw-opacity-0  tw-scale-95"
                  enter-to-class=" tw-transform  tw-opacity-100  tw-scale-100"
                  leave-active-class=" tw-transition  tw-ease-in  tw-duration-75"
                  leave-from-class=" tw-transform  tw-opacity-100  tw-scale-100"
                  leave-to-class=" tw-transform  tw-opacity-0  tw-scale-95"
                >
                  <MenuItems
                    class="
                      tw-origin-top-right
                      tw-absolute
                      tw-right-0
                      tw-mt-2
                      tw-w-48
                      tw-rounded-md
                      tw-shadow-lg
                      tw-py-1
                      tw-bg-white
                      tw-ring-1
                      tw-ring-black
                      tw-ring-opacity-5
                      focus:
                      tw-outline-none
                    "
                  >
                    <MenuItem
                      v-for="item in userNavigation"
                      :key="item.name"
                      v-slot="{ active }"
                    >
                      <a
                        :href="item.href"
                        :class="[
                          active ? ' tw-bg-gray-100' : '',
                          ' tw-block  tw-px-4  tw-py-2  tw-text-sm  tw-text-gray-700',
                        ]"
                        >{{ item.name }}</a
                      >
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <div class="-tw-mr-2 tw-flex md:tw-hidden">
            <!-- Mobile menu button -->
            <DisclosureButton
              class="
                tw-bg-gray-800
                tw-inline-flex
                tw-items-center
                tw-justify-center
                tw-p-2
                tw-rounded-md
                tw-text-gray-400
                hover:tw-text-white hover:tw-bg-gray-700
                focus:tw-outline-none
                focus:tw-ring-2
                focus:tw-ring-offset-2
                focus:tw-ring-offset-gray-800
                focus:tw-ring-white
              "
            >
              <span class="tw-sr-only">Open main menu</span>
              <MenuIcon
                v-if="!open"
                class="tw-block tw-h-6 tw-w-6"
                aria-hidden="true"
              />
              <XIcon v-else class="tw-block tw-h-6 tw-w-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="md:tw-hidden">
        <div class="tw-px-2 tw-pt-2 tw-pb-3 tw-space-y-1 sm:tw-px-3">
          <DisclosureButton
            v-for="item in navigation"
            :key="item.name"
            as="a"
            :href="item.href"
            :class="[
              item.current
                ? 'tw-bg-gray-900 tw-text-white'
                : 'tw-text-gray-300 hover:tw-bg-gray-700 hover:tw-text-white',
              'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium',
            ]"
            :aria-current="item.current ? 'page' : undefined"
            >{{ item.name }}</DisclosureButton
          >
        </div>
        <div class="tw-pt-4 tw-pb-3 tw-border-t tw-border-gray-700">
          <div class="tw-flex tw-items-center tw-px-5">
            <div class="tw-flex-shrink-0">
              <img
                class="tw-h-10 tw-w-10 tw-rounded-full"
                :src="user.imageUrl"
                alt=""
              />
            </div>
            <div class="tw-ml-3">
              <div
                class="
                  tw-text-base tw-font-medium tw-leading-none tw-text-white
                "
              >
                {{ user.name }}
              </div>
              <div
                class="
                  tw-text-sm tw-font-medium tw-leading-none tw-text-gray-400
                "
              >
                {{ user.email }}
              </div>
            </div>
            <button
              type="button"
              class="
                tw-ml-auto
                tw-bg-gray-800
                tw-flex-shrink-0
                tw-p-1
                tw-rounded-full
                tw-text-gray-400
                hover:tw-text-white
                focus:tw-outline-none
                focus:tw-ring-2
                focus:tw-ring-offset-2
                focus:tw-ring-offset-gray-800
                focus:tw-ring-white
              "
            >
              <span class="tw-sr-only">View notifications</span>
              <BellIcon class="tw-h-6 tw-w-6" aria-hidden="true" />
            </button>
          </div>
          <div class="tw-mt-3 tw-px-2 tw-space-y-1">
            <DisclosureButton
              v-for="item in userNavigation"
              :key="item.name"
              as="a"
              :href="item.href"
              class="
                tw-block tw-px-3 tw-py-2 tw-
                rounded-md
                tw-text-base tw-font-medium tw-text-gray-400
                hover:tw-text-white hover:tw-bg-gray-700
              "
              >{{ item.name }}</DisclosureButton
            >
          </div>
        </div>
      </DisclosurePanel>
    </Disclosure>

    <header class="tw-bg-white tw-shadow">
      <div
        class="tw-max-w-7xl tw-mx-auto tw-py-6 tw-px-4 sm:tw-px-6 lg:tw-px-8"
      >
        <h1 class="tw-text-3xl tw-font-bold tw-text-gray-900">Dashboard</h1>
      </div>
    </header>
    <router-view />
  </div>
</template>

<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { BellIcon, MenuIcon, XIcon } from "@heroicons/vue/outline";

const user = {
  name: "Tom Cook",
  email: "tom@example.com",
  imageUrl:
    "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
};
const navigation = [
  { name: "Dashboard", href: "#", current: true },
  { name: "Team", href: "#", current: false },
  { name: "Projects", href: "#", current: false },
  { name: "Calendar", href: "#", current: false },
  { name: "Reports", href: "#", current: false },
];
const userNavigation = [
  { name: "Your Profile", href: "#" },
  { name: "Settings", href: "#" },
  { name: "Sign out", href: "#" },
];
</script>