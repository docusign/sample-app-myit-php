import React from "react";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import { useNavigate } from "react-router-dom";
import parse from "html-react-parser";
import {
  LOADING_STATE_PENDING,
  LOADING_STATE_IDLE,
} from "../../state/constants";
import { Loader } from "../../components";
import {
  FeatureCard,
  ResoursesSection,
  CTASection,
  FeaturesSection,
  TitleSection,
} from "./components";
import feature1Icon from "../../assets/img/feature1.png";
import feature2Icon from "../../assets/img/feature2.png";
import feature3Icon from "../../assets/img/feature3.png";

export const Home = () => {
  const appState = useSelector((state) => state.app);
  const { t } = useTranslation("Home");
  const navigate = useNavigate();

  const handleClick = async (event, redirectUrl) => {
    event.preventDefault();
    navigate(redirectUrl);
  };

  const resourceList = [
    {
      name: t("Resources.PhpSignatureSdk"),
      href: "https://developers.docusign.com/docs/esign-rest-api/sdk-tools/php/",
    },
    {
      name: t("Resources.PhpAdminSdk"),
      href: "https://github.com/docusign/docusign-admin-php-client",
    },
  ];

  return (
    <div className="home-page">
      <Loader show={appState.loading === LOADING_STATE_PENDING} />
      <TitleSection title={t("Header1")} subTitle={parse(t("Header2"))} />
      <FeaturesSection>
        <FeatureCard
          imgSrc={feature1Icon}
          title={parse(t("Card1.Title"))}
          description={parse(t("Card1.Description"))}
          featuresDescription={parse(t("Card1.Features"))}
          buttonTitle={t("Card1.Button")}
          canClick={appState.loading === LOADING_STATE_IDLE}
          onClick={(event) =>
            handleClick(event, "/equipment_and_software_approval")
          }
        />
        <FeatureCard
          imgSrc={feature2Icon}
          title={parse(t("Card2.Title"))}
          description={parse(t("Card2.Description"))}
          featuresDescription={parse(t("Card2.Features"))}
          buttonTitle={t("Card2.Button")}
          canClick={appState.loading === LOADING_STATE_IDLE}
          onClick={(event) =>
            handleClick(event, "/bulk_employee_permission_profile_modification")
          }
        />
        <FeatureCard
          imgSrc={feature3Icon}
          title={parse(t("Card3.Title"))}
          description={parse(t("Card3.Description"))}
          featuresDescription={parse(t("Card3.Features"))}
          buttonTitle={t("Card3.Button")}
          canClick={appState.loading === LOADING_STATE_IDLE}
          onClick={(event) => handleClick(event, "/employee_monitoring")}
        />
      </FeaturesSection>
      <CTASection
        title={t("Footer1")}
        description={parse(t("Footer2"))}
        primaryLink={{
          name: t("SandBoxButton"),
          href: "https://go.docusign.com/o/sandbox/",
        }}
        secondaryLink={{
          name: t("LearnMoreButton"),
          href: "https://developers.docusign.com/",
        }}
      />
      <ResoursesSection
        title={t("Resources.Title")}
        resourceList={resourceList}
      />
    </div>
  );
};
