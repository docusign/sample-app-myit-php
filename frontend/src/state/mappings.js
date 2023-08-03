export const mapIncomingAlert = (alert) => ({
  id: `${alert.user}_${alert.createdAt}_${alert.event}`,
  user: alert.user,
  event: alert.event,
  localizationKey: alert.localizationKey,
  createdAt: alert.createdAt
});
