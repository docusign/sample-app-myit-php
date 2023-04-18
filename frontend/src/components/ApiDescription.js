import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";

export const ApiDescription = ({ description }) => {
  const { t } = useTranslation("Common");
  return (
    <div className="col-lg-4">
      <h1 className="mb-4">{t("ApiDecription.Title")}</h1>
      <div className="accordion" id="accordionSeeMore">
        <div className="accordion-item">
          <h2 className="accordion-header" id="headingOne">
            <button
              className="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseOne"
              aria-expanded="false"
              aria-controls="collapseOne"
            >
              {t("ApiDecription.SeeMore")}
            </button>
          </h2>
          <div
            id="collapseOne"
            className="accordion-collapse collapse"
            aria-labelledby="headingOne"
            data-bs-parent="#accordionSeeMore"
          >
            <div className="accordion-body">
              <div className="card-body">{description}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

ApiDescription.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  description: PropTypes.array.isRequired,
};
