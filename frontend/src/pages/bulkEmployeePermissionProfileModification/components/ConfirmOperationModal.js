import React from "react";
import PropTypes from "prop-types";
import { Button, Modal } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import warning from "../../../assets/img/warning.png";

export const ConfirmOperationModal = ({ show, onCancel, onConfirm }) => {
  const { t } = useTranslation("BulkEmployeePermissionProfileModification");
  return (
    <Modal
      show={show}
      onHide={onCancel}
      size="lg"
      aria-labelledby="contained-modal-title-vcenter"
      centered
    >
      <Modal.Header closeButton />
      <Modal.Body>
        <div className="modal-img-wrapper d-flex justify-content-center">
          <img src={warning} alt="" />
        </div>
        <h1 className="modal-title">{t("ConfirmOperationModal.Title")}</h1>
        <p className="modal-description">
          {t("ConfirmOperationModal.Description")}
        </p>
      </Modal.Body>
      <Modal.Footer className="d-flex justify-content-center">
        <div className="d-flex gap-3">
          <Button
            className="btn-secondary btn-secondary-gray"
            onClick={onCancel}
          >
            <span className="gradient-text">
              {t("ConfirmOperationModal.ButtonClose")}
            </span>
          </Button>
          <Button onClick={onConfirm}>
            {t("ConfirmOperationModal.ButtonConfirm")}
          </Button>
        </div>
      </Modal.Footer>
    </Modal>
  );
}

ConfirmOperationModal.propTypes = {
  show: PropTypes.bool.isRequired,
  onCancel: PropTypes.func.isRequired,
  onConfirm: PropTypes.func.isRequired,
};
