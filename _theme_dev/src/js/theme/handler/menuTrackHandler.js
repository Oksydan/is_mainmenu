/**
 * Menu track handler
 * @param {HTMLElement} trackElement
 */
const menuTrackHandler = (trackElement) => {

  /**
   * Get calculated depth
   * @param {Number} depth
   * @return {number|*}
   */
  const getDepthToCalc = (depth) => {
    return depth > 0 ? depth - 1 : depth;
  }

  /**
   * Move track to depth
   * @param {Number} depth
   * @return {void}
   */
  const moveTrackToDepth = (depth) => {
    if (!trackElement) {
      return;
    }

    trackElement.style.transform = `translateX(-${getDepthToCalc(depth) * 100}%)`;
  }

  return {
    moveTrackToDepth
  }
}

export default menuTrackHandler;
