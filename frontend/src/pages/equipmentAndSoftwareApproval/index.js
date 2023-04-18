import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import parse from "html-react-parser";
import { useSelector, useDispatch } from "react-redux";
import { RequestForm, SuccessOperationResultModal } from "./components";
import { Loader, ApiDescription } from "../../components";
import {
  Step1SelectEmployeesIndex,
  Step2AssignmentToEmployeesIndex,
} from "./consts";
import {
  handleUserChange,
  assignSoftwareAndEquipment,
  setIdleLoading,
} from "../../state/appSlice";

import {
  LOADING_STATE_PENDING,
  LOADING_STATE_SUCCESS,
} from "../../state/constants";

export const EquipmentAndSoftwareApproval = () => {
  const dispatch = useDispatch();
  const appState = useSelector((state) => state.app);
  const [currentStepIndex, setCurrentStepIndex] = useState(
    Step1SelectEmployeesIndex
  );

  const { t } = useTranslation("EquipmentAndSoftwareApproval");

  const handleOnRecipientChange = (event) => dispatch(handleUserChange(event));

  const changePageIndex = (index) => {
    setCurrentStepIndex(index);
  };
  const handleGoToAssignments = () =>
    changePageIndex(Step2AssignmentToEmployeesIndex);

  const handleGoBackToAssignments = () =>
    changePageIndex(Step1SelectEmployeesIndex);

  const hanbleSubmitAssignments = async () => {
    const employees = appState.users
      .filter((user) => user.selected)
      .map((user) => ({
        id: user.id,
        name: user.name,
        email: user.email,
        equipment_ids: user.equipment,
        software_ids: user.software,
      }));
    dispatch(assignSoftwareAndEquipment({ employees }));
  };

  const handleCloseSuccessResultModal = () => {
    dispatch(setIdleLoading());
    changePageIndex(Step1SelectEmployeesIndex);
  };

  const canGoToAssignments = () =>
    appState.users.length > 0 && appState.users.some((r) => r.selected);

  const canSubmitAssignments = () =>
    appState.users.length > 0 &&
    appState.users.some((r) => r.selected) &&
    appState.users
      .filter((r) => r.selected)
      .every(
        (r) =>
          r.email &&
          r.software &&
          r.software?.some((x) => x) &&
          r.equipment &&
          r.equipment?.some((x) => x)
      );

  return (
    <section className="equipment-and-software-approval-page">
      <div className="container">
        <div className="row">
          <RequestForm
            currentStepIndex={currentStepIndex}
            recipients={appState.users}
            selectedRecipients={appState.users.filter((e) => e.selected)}
            software={appState.software}
            equipment={appState.equipment}
            onChange={handleOnRecipientChange}
            onGoToAssignments={handleGoToAssignments}
            canGoToAssignments={canGoToAssignments()}
            onBackToAssignments={handleGoBackToAssignments}
            onSubmitAssignments={hanbleSubmitAssignments}
            canSubmitAssignments={canSubmitAssignments()}
          />
          <ApiDescription description={parse(t("ApiDecription"))} />
          <SuccessOperationResultModal
            show={appState.loading === LOADING_STATE_SUCCESS}
            onHide={handleCloseSuccessResultModal}
          />
          <Loader show={appState.loading === LOADING_STATE_PENDING} />
        </div>
      </div>
    </section>
  );
};
