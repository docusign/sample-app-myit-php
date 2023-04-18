import React from "react";
import PropTypes from "prop-types";

export const FeatureCard = ({
  imgSrc,
  title,
  description,
  featuresDescription,
  buttonTitle,
  canClick,
  onClick,
}) => (
  <div className="d-flex col-4">
    <div className="card-info">
      <div className="card-info-image-holder">
        <img src={imgSrc} alt="" />
      </div>
      <h2 className="card-info-title">{title}</h2>
      <span className="card-info-description">{description}</span>
      <div className="card-info-button-holder">
        <button
          type="button"
          className="btn btn-primary"
          onClick={onClick}
          disabled={!canClick}
        >
          {buttonTitle}
        </button>
      </div>
      <div className="card-info-list">{featuresDescription}</div>
    </div>
  </div>
);
FeatureCard.propTypes = {
  imgSrc: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  description: PropTypes.oneOfType([PropTypes.string, PropTypes.element])
    .isRequired,
  featuresDescription: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
  buttonTitle: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
  canClick: PropTypes.bool.isRequired,
};
