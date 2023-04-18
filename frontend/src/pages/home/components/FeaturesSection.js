import React from "react";
import PropTypes from "prop-types";

export const FeaturesSection = ({ children }) => (
  <section className="features-section">
    <div className="container">
      <div className="row d-flex justify-content-center"> {children}</div>
    </div>
  </section>
);

FeaturesSection.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};
