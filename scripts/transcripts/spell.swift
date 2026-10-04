import AppKit
import Foundation
// reads words (one per line) from stdin, prints those NSSpellChecker flags as misspelled in German
let checker = NSSpellChecker.shared
let LANG = CommandLine.arguments.count > 1 ? CommandLine.arguments[1] : "de"
checker.setLanguage(LANG)
var lang = checker.language()
FileHandle.standardError.write("language: \(lang)\n".data(using: .utf8)!)
while let line = readLine() {
    let w = line.trimmingCharacters(in: .whitespaces)
    if w.isEmpty { continue }
    let r = checker.checkSpelling(of: w, startingAt: 0, language: LANG, wrap: false, inSpellDocumentWithTag: 0, wordCount: nil)
    if r.location != NSNotFound { print(w) }
}
