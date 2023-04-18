import React from "react";
import PropTypes from "prop-types";
import { Button, Modal } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import success from "../../../assets/img/success.png";

export const SuccessOperationResultModal = ({ show, onHide }) => {
  const { t } = useTranslation("EquipmentAndSoftwareApproval");
  return (
    <Modal
      show={show}
      onHide={onHide}
      size="lg"
      aria-labelledby="contained-modal-title-vcenter"
      centered
    >
      <Modal.Header closeButton />
      <Modal.Body>
        <div className="modal-img-wrapper d-flex justify-content-center">
          <img src={success} alt="" />
        </div>
        <h1 className="modal-title">{t("OperationResult.SuccessTitle")}</h1>
        <p className="modal-description">
          {t("OperationResult.SuccessDescription")}
        </p>
      </Modal.Body>
      <Modal.Footer className="d-flex justify-content-center">
        <Button className="btn-secondary btn-secondary-gray" onClick={onHide}>
          <span className="gradient-text">{t("Close")}</span>
        </Button>
      </Modal.Footer>
    </Modal>
  );
}

SuccessOperationResultModal.propTypes = {
  show: PropTypes.bool.isRequired,
  onHide: PropTypes.func.isRequired,
};
