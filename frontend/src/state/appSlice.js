/* eslint-disable no-param-reassign */
import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import {
  getEquipmentAndSoftware,
  getUsers,
  getProfiles,
  getAlerts,
  submitPermissionProfiles,
  submitBulkEnvelope,
  exportDashboard,
} from "../api";

import {
  EVENT_USER_UPDATED,
  LOADING_STATE_IDLE,
  LOADING_STATE_PENDING,
  LOADING_STATE_SUCCESS,
  LOADING_STATE_FAILED,
  initialState,
} from "./constants";

import { mapIncomingAlert } from "./mappings";

export const loadIntitialData = createAsyncThunk(
  "app/loadIntitialData",
  async () => {
    const users = await getUsers();
    const equipAndSoft = await getEquipmentAndSoftware();
    const profiles = await getProfiles();
    return { users, equipAndSoft, profiles };
  }
);

export const loadAlertsData = createAsyncThunk(
  "app/loadAlertsData",
  async () => {
    const alerts = await getAlerts();
    return alerts;
  }
);

export const assignPermissionProfiles = createAsyncThunk(
  "app/assignPermissionProfilesStatus",
  async (data) => {
    await submitPermissionProfiles(data);
  }
);

export const assignSoftwareAndEquipment = createAsyncThunk(
  "app/assignSoftwareAndEquipmentStatus",
  async (data) => {
    await submitBulkEnvelope(data);
  }
);

export const exportDashboardData = createAsyncThunk(
  "app/exportDashboardDataStatus",
  async (fileName) => {
    await exportDashboard(fileName);
  }
);

export const appSlice = createSlice({
  name: "app",
  initialState,
  reducers: {
    handleUserChange: (state, action) => {
      const { id, name, value } = action.payload;
      const initialUser = state.initialUsers.find((u) => u.id === id);
      return {
        ...state,
        users: state.users.map((user) =>
          user.id === id
            ? { ...user, [name]: value, isDirty: value !== initialUser[name] }
            : user
        ),
      };
    },
    rollbackUsersChanges: (state) => {
      state.users = state.initialUsers.map((user) => ({ ...user }));
    },
    setIdleLoading: (state) => {
      state.loading = LOADING_STATE_IDLE;
    },
    setPendingLoading: (state) => {
      state.loading = LOADING_STATE_PENDING;
    },
    addAlert: (state, action) => {
      const alert = mapIncomingAlert(action.payload, state.users);
      state.alerts = [alert, ...state.alerts].sort(
        (a, b) => new Date(b.createdAt) - new Date(a.createdAt)
      );
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(loadIntitialData.pending, (state) => {
        state.loading = LOADING_STATE_PENDING;
      })
      .addCase(loadIntitialData.rejected, (state) => {
        state.loading = LOADING_STATE_FAILED;
      })
      .addCase(loadIntitialData.fulfilled, (state, action) => {
        state.loading = LOADING_STATE_IDLE;
        state.users = action.payload.users.map((user) => ({
          id: user.id,
          selected: false,
          name: user.name,
          email: user.email,
          profileId: user.permissionProfile.id,
        }));
        state.initialUsers = action.payload.users.map((user) => ({
          id: user.id,
          selected: false,
          name: user.name,
          email: user.email,
          profileId: user.permissionProfile.id,
        }));
        state.historyUsers = action.payload.users.map((user) => ({
          id: user.id,
          profileIds: [user.permissionProfile.id],
        }));
        state.software = action.payload.equipAndSoft.software.map((soft) => ({
          label: soft.name,
          value: soft.id,
        }));
        state.equipment = action.payload.equipAndSoft.equipments.map(
          (equip) => ({
            label: equip.name,
            value: equip.id,
          })
        );
        state.profiles = action.payload.profiles.map((profile) => ({
          value: profile.id,
          label: profile.name,
        }));
      })
      .addCase(loadAlertsData.pending, (state) => {
        state.loading = LOADING_STATE_PENDING;
      })
      .addCase(loadAlertsData.rejected, (state) => {
        state.loading = LOADING_STATE_FAILED;
      })
      .addCase(loadAlertsData.fulfilled, (state, action) => {
        state.loading = LOADING_STATE_IDLE;
        const alertList = action.payload;
        state.alerts = [
          ...alertList
            .filter((a) => a.event === EVENT_USER_UPDATED)
            .map(mapIncomingAlert),
          ...state.alerts.filter(a => a.event !== EVENT_USER_UPDATED),
        ].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
      })
      .addCase(assignPermissionProfiles.pending, (state) => {
        state.loading = LOADING_STATE_PENDING;
      })
      .addCase(assignPermissionProfiles.rejected, (state) => {
        state.loading = LOADING_STATE_FAILED;
      })
      .addCase(assignPermissionProfiles.fulfilled, (state) => {
        state.loading = LOADING_STATE_SUCCESS;
        state.users = state.users.map((user) => ({
          ...user,
          isDirty: false,
        }));
        state.initialUsers = state.users.map((user) => ({
          ...user,
        }));
        state.historyUsers = state.historyUsers.map((historyUser) => {
          const submittedUser = state.users.find(
            (user) => user.id === historyUser.id && user.isDirty
          );
          return submittedUser
            ? {
                ...historyUser,
                profileIds: [
                  ...historyUser.profileIds,
                  submittedUser.profileId,
                ],
              }
            : historyUser;
        });
      })
      .addCase(assignSoftwareAndEquipment.pending, (state) => {
        state.loading = LOADING_STATE_PENDING;
      })
      .addCase(assignSoftwareAndEquipment.rejected, (state) => {
        state.loading = LOADING_STATE_FAILED;
      })
      .addCase(assignSoftwareAndEquipment.fulfilled, (state) => {
        state.loading = LOADING_STATE_SUCCESS;
        state.users = state.users.map((user) => ({
          ...user,
          selected: false,
          isDirty: false,
        }));
        state.initialUsers = state.users.map((user) => ({
          ...user,
        }));
      })
      .addCase(exportDashboardData.pending, (state) => {
        state.loading = LOADING_STATE_PENDING;
      })
      .addCase(exportDashboardData.rejected, (state) => {
        state.loading = LOADING_STATE_FAILED;
      })
      .addCase(exportDashboardData.fulfilled, (state) => {
        state.loading = LOADING_STATE_IDLE;
      });
  },
});

export const {
  handleUserChange,
  rollbackUsersChanges,
  setIdleLoading,
  setPendingLoading,
  addAlert,
} = appSlice.actions;

export default appSlice.reducer;
