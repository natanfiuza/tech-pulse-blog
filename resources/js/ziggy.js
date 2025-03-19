const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"home":{"uri":"\/","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"logout":{"uri":"logout","methods":["POST"]},"posts.show":{"uri":"post\/show\/{slug}","methods":["GET","HEAD"],"parameters":["slug"]},"post.index":{"uri":"admin\/home","methods":["GET","HEAD"]},"posts.index":{"uri":"admin\/posts","methods":["GET","HEAD"]},"posts.create":{"uri":"admin\/posts\/create","methods":["GET","HEAD"]},"posts.store":{"uri":"admin\/posts\/store","methods":["POST"]},"posts.edit":{"uri":"admin\/posts\/edit\/{uuid}","methods":["GET","HEAD"],"parameters":["uuid"]},"posts.update":{"uri":"admin\/posts\/update","methods":["PUT"]},"categories.index":{"uri":"categories","methods":["GET","HEAD"]},"categories.store":{"uri":"categories\/store","methods":["POST"]},"categories.create":{"uri":"categories\/create","methods":["GET","HEAD"]},"categories.edit":{"uri":"categories\/{category}\/edit","methods":["GET","HEAD"],"parameters":["category"],"bindings":{"category":"slug"}},"categories.update":{"uri":"categories\/{category}","methods":["PUT"],"parameters":["category"],"bindings":{"category":"slug"}},"categories.destroy":{"uri":"categories\/{category}","methods":["DELETE"],"parameters":["category"],"bindings":{"category":"slug"}},"categories.show":{"uri":"categories\/{slug}","methods":["GET","HEAD"],"parameters":["slug"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
