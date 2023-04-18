import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import parse from "html-react-parser";
import { useSelector, useDispatch } from "react-redux";
import {
  handleUserChange,
  assignPermissionProfiles,
  setIdleLoading,
} from "../../state/appSlice";

import {
  LOADING_STATE_PENDING,
  LOADING_STATE_SUCCESS,
} from "../../state/constants";

import {
  RequestForm,
  SuccessOperationResultModal,
  ConfirmOperationModal,
} from "./components";
import { Loader, ApiDescription } from "../../components";

export const BulkEmployeePermissionProfileModification = () => {
  const appState = useSelector((state) => state.app);
  const dispatch = useDispatch();
  const [confirmation, setConfirmation] = useState({ show: false, data: {} });
  const { t } = useTranslation("BulkEmployeePermissionProfileModification");

  const validateProfileChange = (userId, profileId) => {
    const initialProfileId = appState.initialUsers.find(
      (u) => u.id === userId
    ).profileId;
    const profilesHistory = appState.historyUsers.find(
      (u) => u.id === userId
    ).profileIds;
    return (
      initialProfileId === profileId ||
      profilesHistory.every((id) => id !== profileId)
    );
  };

  const handleOnUserChange = (event) => {
    const { id, value } = event;
    const isValid = validateProfileChange(id, value);
    if (isValid) {
      dispatch(handleUserChange(event));
    } else {
      setConfirmation({ show: true, data: event });
    }
  };

  const handleCancelConfirmation = () => {
    setConfirmation({ show: false, data: {} });
  };

  const handleConfirmConfirmation = () => {
    dispatch(handleUserChange(confirmation.data));
    setConfirmation({ show: false, data: {} });
  };

  const hanbleSubmitAssignments = async () => {
    const employees = appState.users
      .filter((u) => u.isDirty)
      .map((user) => ({
        id: user.id,
        permission_profile_id: user.profileId,
      }));
    dispatch(assignPermissionProfiles({ employees }));
  };

  const handleCloseSuccessResultModal = () => {
    dispatch(setIdleLoading());
  };

  const canSubmitAssignments = () =>
    appState.users.length > 0 && appState.users.some((u) => u.isDirty);

  return (
    <section className="bulk-employee-permission-profile-modification">
      <div className="container">
        <div className="row">
          <RequestForm
            users={appState.users}
            profiles={appState.profiles}
            onChange={handleOnUserChange}
            onSubmitAssignments={hanbleSubmitAssignments}
            canSubmitAssignments={canSubmitAssignments()}
          />
          <ApiDescription description={parse(t("ApiDecription"))} />
          <SuccessOperationResultModal
            show={appState.loading === LOADING_STATE_SUCCESS}
            onHide={handleCloseSuccessResultModal}
          />
          <ConfirmOperationModal
            show={confirmation.show}
            onCancel={handleCancelConfirmation}
            onConfirm={handleConfirmConfirmation}
          />
          <Loader show={appState.loading === LOADING_STATE_PENDING} />
        </div>
      </div>
    </section>
  );
};
